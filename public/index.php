<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Dompdf\Dompdf;

require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$app = new \Slim\App;

$app->get('/', function (Request $request, Response $response) {
    $template = file_get_contents('template/index.html');

    $template = str_replace(['{{ANALYTICS_CODE}}'], [getenv('ANALYTICS_CODE')], $template);

    $response->getBody()->write($template);

    return $response;
});

$app->post('/', function (Request $request, Response $response) {
    $data = $request->getParsedBody();

    if (!isset($data['html'])) {
        $response->getBody()->write("ERROR!");
        return $response;
    }
    $orientation = 'portrait';
    if (isset($data['orientation']) && $data['orientation'] == 'landscape') {
        $orientation = 'landscape';
    }

    $dompdf = new Dompdf();
    $dompdf->loadHtml($data['html']);
    $dompdf->setPaper('A4', $orientation);
    $dompdf->render();
    $response = $response->withHeader('Content-type', 'application/pdf')
        ->withHeader('Content-Disposition', 'attachment; filename="download.pdf"');
    $response->getBody()->write($dompdf->output());

    return $response;
});

$app->run();