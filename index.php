<?php
use GitWrapper\Event\GitLoggerListener;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require 'vendor/autoload.php';
const USERNAME = 'cn=tucq,cn=member,dc=gitst,dc=server';
const PASSWORD = '123';
const DOMAIN = 'gitst.server';
const BASE_DN = 'dc=gitst,dc=server';

// Create the configuration array.
$config = [
    // Mandatory Configuration Options
    'domain_controllers'    => [DOMAIN],
    'base_dn'               => BASE_DN
];

// Create a new Adldap Provider instance.
$provider = new \Adldap\Connections\Provider($config);
$gitWrapper = new GitWrapper\GitWrapper();

try {
    if ($provider->auth()->attempt(USERNAME, PASSWORD)) {
        // Credentials were correct.
        $search = $provider->search();
        $record = $search->findByDn(USERNAME)->getAttributes();

        dd($record);

        //Git stuff

        // Log to a file named "git.log"
        $log = new Logger('git');
        $log->pushHandler(new StreamHandler('git.log', Logger::DEBUG));

        // Instantiate the listener, add the logger to it, and register it.
        $listener = new GitLoggerListener($log);
        $gitWrapper->addLoggerListener($listener);

        $git = $gitWrapper->workingCopy(__DIR__ . '/repos/21welding');
        if ($git->isCloned()) {
            $output = $gitWrapper->git('shortlog -sne');
            dd($output);
            // $output = $git->wrapper->git('shortlog -sne');
        } else {
            $git = $gitWrapper->clone('https://tucq88:1qaZXsw2@bitbucket.org/tucq88/21welding.git', 'repos/21welding');
        }

        dd('Authenticated');
    } else {
        // Credentials were incorrect.
        dd('Incorrect info');
    }

} catch (Exception $e) {
    dd($e);
}

function dump($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
function dd($var) {
    dump($var);die;
}