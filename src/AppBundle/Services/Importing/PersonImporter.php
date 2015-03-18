<?php


namespace AppBundle\Services\Importing;


use AppBundle\Entity\Person;
use AppBundle\Exceptions\InvalidFormatException;
use AppBundle\Services\Enum\Skill;
use Doctrine\Bundle\DoctrineBundle\Registry;

class PersonImporter
{
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function import(PersonImportParameters $params)
    {
        $reader = $params->getFile()->openFile();

        $people = $this->parse($reader);

        $new_count = 0;
        foreach ($people as $person) {
            $new_count += $this->insert($person);
        }

        //TODO return?
    }

    private function parse(\SplFileObject $reader)
    {
        $people = [];

        $this->parse_header($reader, 'Full Members');

        $keys = $reader->fgetcsv();
        $this->verify_keys($keys, ['CID', 'Login', 'First Name', 'Surname', 'Email']);

        $num_keys = count($keys);

        while (!$reader->eof()) {
            $csv = $reader->fgetcsv();

            if (!$this->csv_continue($reader, $csv, $num_keys)) {
                break;
            }

            $data = array_combine($keys, $csv);

            $people[] = $this->to_person($data);
        }

        $this->parse_header($reader, 'Life / Associate');

        $keys = $reader->fgetcsv();
        $this->verify_keys($keys, ['CID/Card Number', 'Login', 'First Name', 'Surname', 'Email']);

        $num_keys = count($keys);

        while (!$reader->eof()) {
            $csv = $reader->fgetcsv();

            if (!$this->csv_continue($reader, $csv, $num_keys)) {
                break;
            }

            $data = array_combine($keys, $csv);

            $people[] = $this->to_person($data);
        }

        return $people;
    }

    private function parse_header(\SplFileObject $reader, $expected)
    {
        $header = trim($reader->fgets());

        if ($header != $expected) {
            throw new InvalidFormatException(sprintf(
                "Header line should be '%s' but was '%s'",
                $expected, $header
            ));
        }
    }

    private function verify_keys($keys, $array)
    {
        return count(array_intersect($keys, $array)) == count($array);
    }

    private function csv_continue(\SplFileObject $reader, array $csv, $num_keys)
    {
        $num_csv = count($csv);
        if ($num_csv == 1 && is_null($csv[0])) {
            return false;
        }

        if ($num_csv != $num_keys) {
            throw new InvalidFormatException(sprintf(
                "Line %i contains %i entries but it should contain %i",
                $reader->key(), $num_csv, $num_keys
            ));
        }

        return true;
    }

    private function to_person(array $data)
    {
        $person = new Person();

        // 'CID', 'Login', 'First Name', 'Surname', 'Email'
        // 'CID/Card Number', 'Login', 'First Name', 'Surname', 'Email'

        if (!empty($data['CID'])) {
            $person->setCid($data['CID']);
        } else {
            $person->setCid($data['CID/Card Number']);
        }

        if (!empty($data['Login'])) {
            $person->setCuser($data['Login']);
        }

        //TODO UTF-8 names get truncated
        $name = $data['First Name'] . ' ' . $data['Surname'];
        $person->setName($name);

        if (!empty($data['Login'])) {
            $person->setEmail($data['Email']);
        }

        return $person;
    }

    private function insert(Person $person)
    {
        // lookup person
        $current_person = $this->doctrine->getRepository('AppBundle:Person')
            ->findOneBy(array(
                'cid' => $person->getCid()
            ));

        if(!$current_person) {
            $person->setSkill(Skill::NOVICE);
            $person->setAdmin(false);

            $em = $this->doctrine->getManager();

            $em->persist($person);
            // if we cared about performance we could share the em...
            $em->flush();
        } else {
            //TODO is there anything to update?
        }

        return !$current_person;
    }
}