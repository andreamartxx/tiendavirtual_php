<?php
namespace ProyectoWeb\repository;

use ProyectoWeb\database\QueryBuilder;
use ProyectoWeb\entity\Product;
use ProyectoWeb\core\Cart;

class ProductRepository extends QueryBuilder
{
    public function __construct(){
        parent::__construct('productos', 'Product');
    }

    public  function getCarrusel()
    {
        $sql = "SELECT * FROM productos WHERE carrusel IS NOT NULL AND carrusel != ''";
        return $this->executeQuery($sql);
        
    }

    public  function getDestacados()
    {
        $sql = "SELECT * FROM productos WHERE destacado = 1";
        return $this->executeQuery($sql);
        
    }

    public  function getNovedades()
    {
        $sql = "SELECT * FROM productos ORDER BY fecha DESC LIMIT 6";
        return $this->executeQuery($sql);
        
    }

    public function getRelacionados(Product $producto)
    {
        $sql = "SELECT * FROM productos WHERE id_categoria = " . $producto->getIdCategoria() .
                " AND id != " . $producto->getId() . " ORDER BY RAND() LIMIT 6";
        return $this->executeQuery($sql);
        
    }
    public function getByCategory(int $idCategoria, int $itemsPerPage, int $currentPage)
    {
        $sql = "SELECT * FROM productos WHERE id_categoria = $idCategoria";
        $sql .= " LIMIT $itemsPerPage OFFSET " .  $itemsPerPage * ($currentPage - 1);
        return $this->executeQuery($sql);
        
    }

    public function getCountByCategory(int $idCategoria)
    {
        $sql = "SELECT count(*) as cuenta FROM productos WHERE id_categoria = $idCategoria";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_ASSOC)["cuenta"];
        
    }

    public function getFromCart(Cart $cart): array
    {
        
        if (empty($cart->getCart())){
            return array();
        }
        $ids = implode(',', array_keys($cart->getCart()));
        $sql = "SELECT * FROM productos WHERE id IN ($ids)";
        return $this->executeQuery($sql);   
    }

}