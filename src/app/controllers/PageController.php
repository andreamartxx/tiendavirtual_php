<?php
namespace ProyectoWeb\app\controllers;

use ProyectoWeb\entity\Category;
use Psr\Container\ContainerInterface;
use ProyectoWeb\repository\ProductRepository;
use ProyectoWeb\repository\CategoryRepository;

class PageController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    public function home($request, $response, $args) {
        $title = "Inicio";
        $repository = new ProductRepository();
        $carrusel = $repository->getCarrusel();
        $destacados = $repository->getDestacados();
        $novedades = $repository->getNovedades();

        $repositoryCateg = new CategoryRepository();
        $categorias = $repositoryCateg->findAll();

        return $this->container->renderer->render($response, "index.view.php", compact('title', 'carrusel', 'destacados', 'novedades', 'categorias'));
    }
}
