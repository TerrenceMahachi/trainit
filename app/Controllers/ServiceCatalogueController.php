<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Servicecategory;
use App\Models\Serviceoffering;
use App\Models\Servicelevelpolicy;
use App\Models\Prioritylevel;

class ServiceCatalogueController extends Controller
{
    /**
     * Admin: Service Catalogue & SLA Policies Console
     */
    public function index()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $categories = Servicecategory::all();
        $offerings = Serviceoffering::all();
        $priorities = Prioritylevel::all();
        $policies = Servicelevelpolicy::all();

        $groupedOfferings = [];
        foreach ($categories as $cat) {
            $catId = is_object($cat) ? $cat->iD : $cat['iD'];
            $catOfferings = Serviceoffering::where('servicecategory', $catId);
            $groupedOfferings[$catId] = [
                'category' => $cat,
                'offerings' => $catOfferings,
            ];
        }

        $this->render('services.catalogue', [
            'groupedOfferings' => $groupedOfferings,
            'categories' => $categories,
            'offerings' => $offerings,
            'priorities' => $priorities,
            'policies' => $policies,
        ]);
    }
}
