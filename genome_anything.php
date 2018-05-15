<?php

array_shift($argv);
if (!count($argv)) {
    echo "Usage: genome_anything.php <genome data file> [max characters]\n";
    exit(1);
}

$genome_filename = array_shift($argv);
$limit = empty($argv[0])?false:array_shift($argv);

$msg_filename = 'character_encoding.dat';
$msg = file_get_contents($msg_filename);

$genome_fh = fopen($genome_filename, 'rb');
$triplet = '';

$count = 0;
while (false !== ($char = fread($genome_fh, 1)) && '' != $char && (!$limit || $count <= $limit)) {
    if (false === strpos('ACGT', $char)) {
        continue;
    }
    $triplet .= $char;
    if (strlen($triplet) == 3) {
        echo decode_triplet($triplet);
        $triplet = '';
        $count++;
    }
}
echo "\n";

function decode_triplet($triplet) {
    static $offset = 0;
    global $msg;

    $k = ord($triplet[0])+ord($triplet[1])+ord($triplet[2]);//because ord('T') is 84 and 84*3 < 255, this is guaranteed to fit inside a byte

    $k = $k ^ ord($msg[$offset]);
    $offset++;
    $offset %= strlen($msg);
    return chr($k);
}
