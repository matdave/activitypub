<?php
abstract class ActivityPubBaseManagerController extends modExtraManagerController {
    /** @var \MatDave\ActivityPub\Service $activitypub */
    public $activitypub;
    public int $lit = 0;

    public int $version;

    public function initialize(): void
    {
        $this->activitypub = $this->modx->services->get('activitypub');

        $this->lit = $this->modx->getOption('activitypub.lit', null, 0);

        $this->addCss($this->activitypub->getOption('cssUrl') . 'mgr.css');
        $this->addJavascript($this->activitypub->getOption('jsUrl') . 'mgr/activitypub.js');
        $this->addJavascript($this->activitypub->getOption('jsUrl') . 'mgr/utils/combos.js');

        $this->addHtml('
            <script type="text/javascript">
                Ext.onReady(function() {
                    activitypub.config = '.$this->modx->toJSON($this->activitypub->config).';
                });
            </script>
        ');

        parent::initialize();
    }

    public function getLanguageTopics(): array
    {
        return array('activitypub:default');
    }

    public function checkPermissions(): bool
    {
        return true;
    }

    /**
     * Add an external Javascript file to the head of the
     * page with cache clearing flag
     *
     * @param string $script
     *
     * @return void
     */
    public function addJavascript($script)
    {
        $this->head['js'][] = $script . "?lit=" . $this->lit;
    }

    /**
     * Add an external CSS file to the head of the
     *  page with cache clearing flag
     *
     * @param string $script
     *
     * @return void
     */
    public function addCss($script)
    {
        $this->head['css'][] = $script. "?lit=" . $this->lit;
    }

    /**
     * Add an external Javascript file to the head of the
     *  page with cache clearing flag
     *
     * @param string $script
     *
     * @return void
     */
    public function addLastJavascript($script)
    {
        $this->head['lastjs'][] = $script . "?lit=" . $this->lit;
    }
}
