<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Notification;
use Illuminate\Http\Request;

class ResponsableController extends BaseActiviteController
{
    protected string $viewPrefix = 'responsable';

    /*public function index(Request $request)
    {
        return $this->indexActivites($request);
    }

    public function create()
    {
        return $this->createActivite();
    }

    public function store(Request $request)
    {
        return $this->storeActivite($request);
    }

    public function edit(Activite $activite)
    {
        return $this->editActivite($activite);
    }

    public function update(Request $request, Activite $activite)
    {
        return $this->updateActivite($request, $activite);
    }

    public function destroy(Activite $activite)
    {
        return $this->destroyActivite($activite);
    }

    public function valider(Activite $activite)
    {
        return $this->validerActivite($activite);
    }*/
}