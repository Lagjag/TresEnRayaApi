<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/", name="app_api", methods={"GET"})
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Conexión exitosa con la api"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Algo ha ocurrido"
     * )
     * 
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    /**
     * @Route("/api/crear_tablero", name="app_api_crear_tablero", methods={"POST"})
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Estado de tablero actualizado"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error al actualizar el tablero"
     * )
     * 
     */
    public function crearTablero()
    {
        return $this->json([
            'message' => 'jajajajaj',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    /**
     * @Route("/api/borrar_tablero", name="app_api_borrar_tablero", methods={"DELETE"})
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Borrado de tablero hecho"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error al borrar el tablero"
     * )
     * 
     */
    public function borrarTablero()
    {
        return $this->json([
            'message' => 'borrar',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    /**
     * @Route("/api/modificar_tablero", name="app_api_modificar_tablero", methods={"PUT"})
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Tablero modificado"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error al modificar tablero"
     * )
     * 
     */
    public function modificarTablero()
    {
        return $this->json([
            'message' => 'borrar',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

}
