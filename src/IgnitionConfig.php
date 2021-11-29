<?php

namespace Facade\Ignition;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class IgnitionConfig implements Arrayable {
    /** @var array */
    protected $options;

    public function __construct( array $options = [] ) {
        $this->options = $this->mergeWithDefaultConfig( $options );
    }

    public function getEditor(): ?string {
        return Arr::get( $this->options, 'editor' );
    }

    public function getRemoteSitesPath(): ?string {
        return Arr::get( $this->options, 'remote_sites_path' );
    }

    public function getLocalSitesPath(): ?string {
        return Arr::get( $this->options, 'local_sites_path' );
    }

    public function getTheme(): ?string {
        return Arr::get( $this->options, 'theme' );
    }

    public function getEnableShareButton(): bool {
        if ( ! app()->isBooted() ) {
            return false;
        }

        return Arr::get( $this->options, 'enable_share_button', true );
    }

    public function getEnableRunnableSolutions(): bool {
        $enabled = Arr::get( $this->options, 'enable_runnable_solutions', null );

        if ( $enabled === null ) {
            $enabled = config( 'app.debug' );
        }

        return $enabled ?? false;
    }

    public function getPhpStormLink(): ?string {
        $phpstormLink = '';
        $editor       = Arr::get( $this->options, 'editor' );

        if ( $editor === 'phpstorm' ) {
            $projectName  = self::phpStormProjectNameInDir();
            $phpstormLink = $projectName ? "jetbrains://php-storm/navigate/reference?project=$projectName&path=%path:%line" : "http://localhost:63342/api/file?file=%path&line=%line";
        }

        return $phpstormLink;
    }

    public function toArray(): array {
        return [
            'editor'                  => $this->getEditor(),
            'remoteSitesPath'         => $this->getRemoteSitesPath(),
            'localSitesPath'          => $this->getLocalSitesPath(),
            'theme'                   => $this->getTheme(),
            'enableShareButton'       => $this->getEnableShareButton(),
            'enableRunnableSolutions' => $this->getEnableRunnableSolutions(),
            'directorySeparator'      => DIRECTORY_SEPARATOR,
            'phpstormLink'            => $this->getPhpStormLink(),
        ];
    }

    protected function mergeWithDefaultConfig( array $options = [] ): array {
        return array_merge( config( 'ignition' ) ?: include __DIR__ . '/../config/ignition.php', $options );
    }

    /**
     * Get name of project from file in provided directory
     *
     * @return string
     */
    static function phpStormProjectNameInDir() {

        $projectName = '';

        $projectPath = base_path();

        $iml_dirs = [
            $projectPath . DIRECTORY_SEPARATOR . '.idea',
            // new project format
            $projectPath
            // old project format
            // TODO Check if 'phpstorm://open?file' works on old PhpStrom without Toolbox
        ];

        foreach ( $iml_dirs as $path ) {
            $projectName = self::getPhpStormProjectNameInDir( $path );
            if ( $projectName ) {
                break;
            }
        }

        return $projectName;

    }

    static function getPhpStormProjectNameInDir( $path ) {
        foreach ( scandir( $path ) as $file ) {

            if ( $file == '.' || $file == '..' ) {
                continue;
            }
            $dir = $path . DIRECTORY_SEPARATOR . $file;

            if ( ! is_dir( $dir ) && strstr( $dir, ".iml" ) ) {
                return pathinfo( $dir, PATHINFO_FILENAME );
            }
        }

        return false;
    }
}
