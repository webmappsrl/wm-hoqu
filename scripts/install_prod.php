<?php
// Use this script to install on production

if($argc<2) {
    usage();
}
else {
    $path = $argv[1];
    echo "Try to install HOQO in $path\n";
    // Check dir
    if (!file_exists($path)) {
        echo "ERROR: $path must be already created, empty or not!\n\n";
        die();
    }

    // Ask confirm
    echo "Do you confirm? Alla data in $path will be removed. To proceed answer YES (YES not yes or Y or y)\n\n";
    $input = rtrim(fgets(STDIN));
    if ($input!=='YES') {
        echo "Aborted (co confirm write YES)\n\n";
        die();
    }
    // Proceed

    // Save config file (if exists)
    $has_conf = false;
    $conf_src = "$path/src/config.json";
    if(file_exists($conf_src)) {
        $has_conf=true;
        $conf_tmp = tempnam('/tmp','hoquconf_');
        $cmd = "cp $conf_src $conf_tmp";
        system($cmd);
    }

    $cmd = "rm -rf $path/*"; system($cmd);
    $cmd = "cp -R ".__DIR__."/../* $path"; system($cmd);
    if ($has_conf) {
        $cmd = "cp $conf_tmp $conf_src";
        system($cmd);
        $cmd = "rm $conf_tmp";
    }
    echo "Software HOQU installed in $path ... ALL DONE\n\n";
}

function usage() : void {
    echo "\n\nUsage: php scripts/install_prod.php PATH\n\n";
    return;
}