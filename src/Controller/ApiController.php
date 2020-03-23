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
     * Función que recupera el tablero en caso de cerrar y querer jugar despues
     * 
     */
    public function recuperarTablero()
    {
        $em = $this->getDoctrine()->getManager();

        $tablero = new Tablero();
        $tablero = $em->getRepository("App:Tablero")->findAll();
        $tablero = end($tablero);
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
     * Función que crea el tablero en caso de que no exista
     * 
     */
    public function crearTablero(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

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
     * @Route("/api/modificar_tablero/", name="app_api_modificar_tablero", methods={"PUT"})
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
     * Función que calcula como se queda el tablero
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
                    $fichaGanadora = explode("-",$casillasGanadoras[0]);
                    $fichaGanadora = $fichaGanadora[1];
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
            'fichaGanadora' => (isset($fichaGanadora))? $fichaGanadora : '',
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
     * Función que graba el tablero dependiendo el parámetro de entrada
     * 
     */
    public function grabarTablero(int $id, Request $request)
    {
        $varTablero = json_decode($request->getContent());
        $em = $this->getDoctrine()->getManager();

        switch($varTablero->accion){
            case 'nuevoMovimiento':
                $estadoTablero = array_slice($varTablero->estado,0,$varTablero->idCasilla);
                $estadoTablero[] = $varTablero->turno;
                $estadoTablero = array_merge($estadoTablero,array_slice($varTablero->estado,$varTablero->idCasilla+1));

                $turno = ($varTablero->turno + 1) % 2;
                
                $tablero = $em->getRepository('App:Tablero')->find($id);
                $tablero->setTurno($turno);
                $tablero->setEstado($estadoTablero);
                $em->flush();

                return $this->json([
                    'tablero' => $estadoTablero,
                    'turno' => $turno
                ]);
                break;
            case 'movimientoIA':
                $puntosIA = [0 => 2, 1 => 0, 2 => 1];
                $vacios = [];
                $puntos = [];

                foreach($varTablero->estado as $clave => $valor){
                    if($valor === 2){
                        $vacios[]=$clave;
                    }
                }
                
                foreach($vacios as $clave => $valor){
                    $punto = 0;
                    foreach($this->patrones as $patron){
                        if(in_array($valor,$patron)){ 
                            $conteoX=0;
                            $conteoO=0;
                            
                            foreach($patron as $valorPatron){
                                if($varTablero->estado[$valorPatron] === 0){
                                    $conteoX++;
                                }else if($varTablero->estado[$valorPatron] === 1){
                                    $conteoO++;
                                }
                                if($valorPatron === $valor){
                                    $punto = $punto + 0;
                                }else{
                                    $punto = $punto + $puntosIA[$varTablero->estado[$valorPatron]];
                                }
                            }
                            if($conteoX >= 2){
                                $punto += 10;
                            }
                            if($conteoO >= 2){
                                $punto += 20;
                            }
                        }
                    }
                    $puntos[]=$punto;
                }

                $indiceMaximo = 0;
                $valorMaximo = 0;
                foreach($puntos as $clave => $valor){
                    if($valor >= $valorMaximo){
                        $indiceMaximo = $clave;
                        $valorMaximo = $valor;
                    }
                }
                
                return $this->json([
                    'valorIA' => $vacios[$indiceMaximo],
                ]);
                break;
            case 'cambioModo':
                $estadoTablero = array_fill(0, 9, 2);
                $turno = 0;

                $tablero = $em->getRepository('App:Tablero')->find($id);
                if(strpos($varTablero->modoJuego, "IA")){
                    $tablero->setModoJuego("IA");
                }else if(strpos($varTablero->modoJuego, "2J")){
                    $tablero->setModoJuego("2J");
                }
                $tablero->setTurno($turno);
                $tablero->setEstado($estadoTablero);
                $em->flush();

                return $this->json([
                    'modoJuego' => $tablero->getModoJuego()
                ]);
                break;
            case 'resetTablero':
                $estadoTablero = array_fill(0, 9, 2);
                $turno = 0;

                $tablero = $em->getRepository('App:Tablero')->find($id);
                $tablero->setTurno($turno);
                $tablero->setEstado($estadoTablero);
                $em->flush();

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
            $casillas[]=$id;
        }
        return $casillas;
    }
}
