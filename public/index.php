<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

/* if ($_SERVER['APP_DEBUG']) {
    header('Access-Control-Allow-Origin:'.rtrim($_SERVER['HTTP_REFERER'], '/'));
} else {
    header('Access-Control-Allow-Origin:https://balade-parfumee-2ydzvfltv-loics-projects-c950e0a8.vercel.app');
}
header('Access-Control-Allow-Headers:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:X-Requested-With, Content-Type, withCredentials');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    die();
} */

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Allow: *");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === "OPTIONS") {
    die();
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';


return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
