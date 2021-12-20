<?php
namespace ProyectoWeb\app\controllers;

use JasonGrimes\Paginator;
use ProyectoWeb\exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ProyectoWeb\repository\ProductRepository;
use ProyectoWeb\repository\CategoryRepository;
use ProyectoWeb\core\App;


class ProductController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    public function ficha($request, $response, $args) {
        extract($args);
        $repositoryCateg = new CategoryRepository();
        $categorias = $repositoryCateg->findAll();
        $repositorio = new ProductRepository();
        try{
            $producto =$repositorio->findById($id);
        }catch(NotFoundException $nfe){
            $response = new \Slim\Http\Response(404);
            return $response->write("Producto no encontrado");
        }
        $title = $producto->getNombre();
        $relacionados = $repositorio->getRelacionados($producto);
        return $this->container->renderer->render($response, "product.view.php", compact('title', 'categorias', 'producto', 'relacionados'));
    }

    public function listado($request, $response, $args) {
        extract($args);
        $currentPage = ($currentPage ?? 1);
        $repositorio = new CategoryRepository();
        $categorias = $repositorio->findAll();
        try{
            $categoriaActual = $repositorio->findById($id);
        }catch(NotFoundException $nfe){
            $response = new \Slim\Http\Response(404);
            return $response->write("Categoría no encontrada");
        }
        $title = $categoriaActual->getNombre();
        $repositorioProductos = new ProductRepository();

        //Datos para el paginador
        $currentPage = ($currentPage ?? 1);
        $totalItems = $repositorioProductos->getCountByCategory($categoriaActual->getId());
        $itemsPerPage = APP::get('config')['itemsPerPage'];
        $urlPattern = $this->container->router->pathFor('categoria', 
        ['nombre' =>  \ProyectoWeb\app\utils\Utils::encodeURI($categoriaActual->getNombre()),
         'id' => $categoriaActual->getId()
        ]) .
        '/page/(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
        
        $productos = $repositorioProductos->getByCategory($categoriaActual->getId(),
         $itemsPerPage, $currentPage);

        
        return $this->container->renderer->render($response, "categoria.view.php", 
                compact('title', 'categorias', 'categoriaActual', 'productos', 'paginator'));
    }
}
