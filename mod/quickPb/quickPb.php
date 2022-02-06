<?php
namespace QuickPb;

const WIRETYPE_VARINT = 0;
const WIRETYPE_DOUBLE = 1;
const WIRETYPE_LENGTH = 2;
const WIRETYPE_GROUPSTART = 3;
const WIRETYPE_GROUPEND = 4;
const WIRETYPE_FLOAT = 5;

const FIELD_NUM = 0;
const FIELD_WIRETYPE = 1;
const FIELD_NEST = 2;

include('utils.php');
include('compiler.php');

class QuickPb {
    function compile() {
        
    }
}