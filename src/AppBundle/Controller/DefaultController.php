<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;
use AppBundle\Entity\Datos;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/geoweather/{date}/{hour}/{lat}/{long}")
     * @Method({"GET"})
     */
    public function geoweatherAction($date, $hour, $lat, $long)
    {
        $weather_array = $this->weatherAction($lat, $long, $date, $hour);
        $geo_array = $this->geoAction($lat, $long);

        $geoweather_array = array_merge($weather_array, $geo_array);

        $this->saveDataAction($geoweather_array);

        $response = new Response();
        $response->setContent(json_encode($geoweather_array, JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function weatherAction($lat, $long, $date, $hour)
    {
        $data = array();

        $data['date'] = $date;
        $data['hour'] = $hour;
        $data['lat'] = $lat;
        $data['lon'] = $long;

        $owm = new OpenWeatherMap();

        $units = $this->container->getParameter('units');
        $lang = $this->container->getParameter('lang');
        $api_key = $this->container->getParameter('api_key');
        $dat = new \DateTime($date);


        $array_lat_lon = array();
        $array_lat_lon['lat'] = $lat;
        $array_lat_lon['lon'] = $long;

        try {
            $weather = $owm->getWeather($array_lat_lon, $units, $lang, $api_key);
//            $weather = $owm->getWeatherHistory($array_lat_lon, $dat, 1, $type = 'hour', $units, $lang, $api_key);
        } catch(OWMException $e) {
            echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
            echo "<br />\n";
        } catch(\Exception $e) {
            echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
            echo "<br />\n";
        }

        $data['weather'] = $weather->weather->description;

        return $data;
    }

    private function geoAction($lat, $long)
    {
        $data = array();

        $geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$long.'&sensor=false');

        $output= json_decode($geocode);

        for($j=0;$j<count($output->results[0]->address_components);$j++){
            if($output->results[0]->address_components[$j]->types[0] == "administrative_area_level_2")
                $data['city'] = $output->results[0]->address_components[$j]->long_name;
            elseif($output->results[0]->address_components[$j]->types[0] == "administrative_area_level_1")
                $data['region'] = $output->results[0]->address_components[$j]->long_name;
            elseif($output->results[0]->address_components[$j]->types[0] == "country")
                $data['country'] = $output->results[0]->address_components[$j]->long_name;
        }

        return $data;
    }

    private function saveDataAction($data)
    {
        $fecha = new \DateTime($data['date'].' '.$data['hour']);

        $datos = new Datos();
        $datos->setDate($fecha);
        $datos->setLat($data['lat']);
        $datos->setLon($data['lon']);
        $datos->setWeather($data['weather']);
        $datos->setCity($data['city']);
        $datos->setRegion($data['region']);
        $datos->setCountry($data['country']);


        $em = $this->getDoctrine()->getManager();

        $em->persist($datos);
        $em->flush();
    }
}
