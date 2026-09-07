<?php
class AdminLinkGenerator extends CodeGenerator
{
    private function label(): string
    {
        return ucfirst($this->modelLower) . 's';
    }

    private function url(): string
    {
        return '/' . $this->modelLower . 's';
    }

    private function updateNav(): void
    {
        $path = GEN_ROOT . '/config/nav.php';
        $content = file_get_contents($path);
        $url = $this->url();

        if (strpos($content, "'url' => '{$url}'") !== false) {
            return;
        }

        $marker = '            // BOILERPLATE_ADMIN_NAV_ITEMS';
        $entry = "            ['label' => '" . $this->label() . "', 'url' => '{$url}'],\n";

        if (strpos($content, $marker) !== false) {
            $content = str_replace($marker, $entry . $marker, $content);
            file_put_contents($path, $content);
        }
    }

    private function updateDashboard(): void
    {
        $path = GEN_ROOT . '/views/dashboard/admin.php';
        $content = file_get_contents($path);

        if (strpos($content, "App\\Models\\{$this->modelName}::countAll()") !== false) {
            return;
        }

        $marker = '                        <!-- BOILERPLATE_ADMIN_DASHBOARD_CARDS -->';
        $label = $this->label();
        $url = $this->url();
        $modelName = $this->modelName;

        $card = <<<PHP
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                            <div class="card card-dashboard-two">
                                <div class="card-body py-4">
                                    <h3> <?= App\Models\\{$modelName}::countAll(); ?> </h3>
                                    <p class="fw-bold">{$label} | <a class="text-success pointer"
                                            href="<?php echo \$siteConfig->siteUrl; ?>{$url}">View</a> </p>

                                </div><!-- card-body -->
                            </div><!-- card -->
                        </div><!-- col -->

PHP;

        if (strpos($content, $marker) !== false) {
            $content = str_replace($marker, $card . $marker, $content);
            file_put_contents($path, $content);
        }
    }

    public function generate()
    {
        $this->updateNav();
        $this->updateDashboard();
    }
}
