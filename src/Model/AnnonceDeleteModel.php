<?php
/**
 * Created by PhpStorm.
 * User: langanfin
 * Date: 17/12/2019
 * Time: 11:48
 */

namespace App\Model;


class AnnonceDeleteModel
{


    private $organisation;

    /**
     * @return mixed
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param mixed $organisation
     */
    public function setOrganisation($organisation): void
    {
        $this->organisation = $organisation;
    }


}