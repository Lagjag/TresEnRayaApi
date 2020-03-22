<?php

namespace App\Controller;

use App\Entity\Tablero;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class ApiController extends AbstractController
{   
    private $patrones = [
        //horizontal
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        //vertical
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        //diagonal
        [0, 4, 8],
        [2, 4, 6]
    ];

    /**
     * @Route("/api/recuperar_tablero", name="app_api_crear_tablero", methods={"GET"})
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Tablero existente"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No hay tablero existente"
     * )
     * 
     */
    public function recuperarTablero()
    {
        $em = $this->getDoctrine()->getManager();

        $tablero = new Tablero();
        $tablero = $em->getRepository("App:Tablero")->findAll();
        $tablero = end($tablero);
        //$tablero = serialize($tablero);
        //$tablero = (array)$tablero;
        return $this->json([
            'id' => ($tablero !== false)? $tablero->getId(): '',
            'tablero' => ($tablero !== false)? $tablero->getEstado(): '',
            'turno' => ($tablero !== false)? $tablero->getTurno(): '',
            'modo_juego' => ($tablero !== false)? $tablero->getModoJuego(): '',
            'existe' => ($tablero !== false)? true : false
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
    public function crearTablero(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /*return $this->json([
            'message' => 'jajajajaj',
            'path' => 'src/Controller/ApiController.php',
        ]);*/
        $data = json_decode($request->getContent(), true);

        $tablero = new Tablero();
        $tablero->setEstado($data);
        $tablero->setTurno(0);
        $tablero->setModoJuego("IA");


        $em->persist($tablero);
        $em->flush();
        
        return $this->json([
            $tablero->getId()
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
     * @Route("/api/modificar_tablero/{id}", name="app_api_modificar_tablero", methods={"GET"})
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
    public function modificarTablero(Request $request)
    {   
        $ganado = false;
        
        $varTablero = json_decode($request->getContent());
        foreach($this->patrones as $patron){
            $primeraCasilla = $varTablero->estado[$patron[0]];

            if($primeraCasilla !== 2){
                $casillasMarcadas = $this->getCasillas($varTablero->estado, $patron, $primeraCasilla);

                if(count($casillasMarcadas) === 3){
                    $casillasGanadoras = $this->iluminarCasillas($patron, $primeraCasilla);
                    $ganado = true;
                }

            }
        }

        if(!in_array(2,$varTablero->estado) && !$ganado){
            $movimiento = 'empatado';
        }elseif($varTablero->modo_juego === 'IA' && $varTablero->turno === 1 && !$ganado){
            $movimiento = 'IA';
        }

        return $this->json([
            'primeraCasilla' => $primeraCasilla,
            'casillasMarcadas' => (isset($casillasMarcadas))? $casillasMarcadas : '',
            'casillasGanadoras' => (isset($casillasGanadoras))? $casillasGanadoras : '',
            'movimiento' => (isset($movimiento))? $movimiento : ''
        ]);
    }

    /**
     * @Route("/api/grabar_tablero/{id}", name="app_api_grabar_tablero", methods={"PUT"})
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Tablero grabado con éxito"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error al grabar el tablero"
     * )
     * 
     */
    public function grabarTablero(int $id, Request $request)
    {
        $varTablero = json_decode($request->getContent());

        switch($varTablero->accion){
            case "nuevoMovimiento":
                $estadoTablero = array_slice($varTablero->estado,0,$varTablero->idCasilla);
                $estadoTablero[] = $varTablero->turno;
                $estadoTablero = array_merge($estadoTablero,array_slice($varTablero->estado,$varTablero->idCasilla+1));

                $turno = ($varTablero->turno + 1) % 2;

                

                return $this->json([
                    'tablero' => $estadoTablero,
                    'turno' => $turno
                ]);
                break;
            default:
        }
    }

    // Función para sacar las correspondencias de casillas
    private function getCasillas($estadoTablero, $patron, $primeraCasilla){
        $casillas = [];
        foreach($estadoTablero as $clave => $valor){
            if(in_array($clave,$patron) && $valor === $primeraCasilla){
                array_push($casillas,$valor);
            }
        }
        return $casillas;
    }

    // Función para iluminar las casillas
    private function iluminarCasillas($patron, $primeraCasilla){
        $casillas = [];
        foreach($patron as $clave => $valor){
            $id = $valor."-".$primeraCasilla;
            array_push($casillas,$id);
        }
        return $casillas;
    }
}
