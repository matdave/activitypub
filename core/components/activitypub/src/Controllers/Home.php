<?php

namespace MatDave\ActivityPub\Controllers;

class Home extends Base
{
    public function process(array $scriptProperties = []): void
    {
    }

    public function getPageTitle(): string
    {
        return $this->modx->lexicon('activitypub.home.page_title');
    }

    public function loadCustomCssJs(): void
    {
        $this->addJavascript($this->activitypub->getOption('jsUrl') . 'mgr/widgets/actor/grid.js');
        $this->addJavascript($this->activitypub->getOption('jsUrl') . 'mgr/widgets/activity/grid.js');

        $this->addJavascript($this->activitypub->getOption('jsUrl') . 'mgr/widgets/home/panel.js');
        $this->addJavascript($this->activitypub->getOption('jsUrl') . 'mgr/sections/home.js');

        $this->addHtml(
            '
            <script type="text/javascript">
                Ext.onReady(function() {
                    MODx.load({ xtype: "activitypub-page-home"});
                });
            </script>
        '
        );
    }

    public function getTemplateFile(): string
    {
        return $this->activitypub->getOption('templatesPath') . 'ext.tpl';
    }

}