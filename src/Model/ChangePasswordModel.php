<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN Rogelio
 * Date: 02/08/2021
 * Time: 19:16
 */

namespace App\Model;


class ChangePasswordModel
{

    private  $oldPassword ;
    private  $newPassword ;

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     */
    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param mixed $oldPassword
     */
    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

}