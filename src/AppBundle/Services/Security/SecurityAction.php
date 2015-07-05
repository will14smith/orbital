<?php


namespace AppBundle\Services\Security;

class SecurityAction
{
    // standard
    const VIEW = 'VIEW';
    const CREATE = 'CREATE';
    const EDIT = 'EDIT';
    const DELETE = 'DELETE';

    // custom
    const ACCEPT = 'ACCEPT';
    const CLAIM = 'CLAIM';
    const ENTER = 'ENTER';
    const START = 'START';
    const END = 'END';
    const JUDGE = 'JUDGE';
    const SCORE = 'SCORE';
    const SIGN = 'SIGN';
    const SIGNUP = 'SIGNUP';
    const SUBMIT = 'SUBMIT';
}
