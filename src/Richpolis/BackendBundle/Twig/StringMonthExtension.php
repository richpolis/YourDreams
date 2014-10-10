<?php

namespace Richpolis\FrontendBundle\Twig;

class StringMonthExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'string_month' => new \Twig_Filter_Method($this, 'stringMonthFilter'),
        );
    }

    public function stringMonthFilter($mes)
    {
        switch($mes)
        {
            case 1: return "Enero";
            case 2: return "Febrero";
            case 3: return "Marzo";
            case 4: return "Abril";
            case 5: return "Mayo";
            case 6: return "Junio";
            case 7: return "Julio";
            case 8: return "Agosto";
            case 9: return "Septiembre";
            case 10: return "Octubre";
            case 11: return "Noviembre";
            case 12: return "Diciembre";
        }

    }

    public function getName()
    {
        return 'string_month_extension';
    }
}