<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SurveillerReseauxContinu extends Command
{
    protected $signature = 'reseaux:surveiller-continu {--intervalle=5 : Frequence de verification en secondes}';
    protected $description = 'Verifie les reseaux sociaux en boucle continue, avec une precision plus fine que le planificateur cron (1 minute)';

    public function handle()
    {
        $intervalle = (int) $this->option('intervalle');

        $this->info("Surveillance continue demarree (verification toutes les {$intervalle}s). Ctrl+C pour arreter.");

        // Evite l'accumulation du log de requetes SQL en memoire sur une longue duree
        DB::connection()->disableQueryLog();

        while (true) {
            try {
                Artisan::call('reseaux:verifier');
            } catch (\Throwable $e) {
                $this->error('Erreur pendant la verification : ' . $e->getMessage());
                \Log::error('Erreur surveillance continue: ' . $e->getMessage());
            }

            // Liberer la memoire des objets Eloquent accumules dans ce cycle
            gc_collect_cycles();

            sleep($intervalle);
        }
    }
}