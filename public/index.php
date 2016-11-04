<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Dompdf\Dompdf;

require 'vendor/autoload.php';

$app = new \Slim\App;

$app->get('/', function (Request $request, Response $response) {
    $template = file_get_contents('template/index.html');
    $response->getBody()->write($template);

    return $response;
});

$app->post('/', function (Request $request, Response $response) {
    $data = $request->getParsedBody();

    if (!isset($data['html'])) {
        $response->getBody()->write("ERROR!");
        return $response;
    }

    $dompdf = new Dompdf();
    $dompdf->loadHtml($data['html']);
    $dompdf->setPaper('A4');
    $dompdf->render();
    $response->getBody()->write($dompdf->stream());

    return $response;
});

$app->run();