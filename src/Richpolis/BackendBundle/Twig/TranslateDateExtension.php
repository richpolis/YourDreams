<?php

namespace Richpolis\FrontendBundle\Twig;

class TranslateDateExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'translate_date' => new \Twig_Filter_Method($this, 'translateDateFilter'),
        );
    }

    public function translateDateFilter($datetime)
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        
        //$dateTime->setTimezone(new \DateTimeZone('America/Mexico_City'));
        $dia=$dateTime->format('l');
        $mes=$dateTime->format('F');
        $ano=$dateTime->format('Y');
        
        if ($dia=="Monday") $dia="Lunes";
        if ($dia=="Tuesday") $dia="Martes";
        if ($dia=="Wednesday") $dia="Miercoles";
        if ($dia=="Thursday") $dia="Jueves";
        if ($dia=="Friday") $dia="Viernes";
        if ($dia=="Saturday") $dia="Sabado";
        if ($dia=="Sunday") $dia="Domingo";
        
        if ($mes=="January") $mes="Enero";
        if ($mes=="February") $mes="Febrero";
        if ($mes=="March") $mes="Marzo";
        if ($mes=="April") $mes="Abril";
        if ($mes=="May") $mes="Mayo";
        if ($mes=="June") $mes="Junio";
        if ($mes=="July") $mes="Julio";
        if ($mes=="August") $mes="Agosto";
        if ($mes=="September") $mes="Septiembre";
        if ($mes=="October") $mes="Octubre";
        if ($mes=="November") $mes="Noviembre";
        if ($mes=="December") $mes="Diciembre";
        
        $dia2=$dateTime->format('d');
        
        return "$dia, $dia2 de $mes de $ano";
    }

    public function getName()
    {
        return 'translate_date_extension';
    }
}