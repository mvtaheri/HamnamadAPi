<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 4/14/2019
 * Time: 12:34 PM
 */

namespace App\Http\Repositories;

interface RepositoryInterface
{
    public function all();

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function show($id);
}
