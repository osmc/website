<?php
    class WBS_Upgrade_Notices
    {
        public function __construct($storageOptionName, $removeNoticeUrlFlagName)
        {
            $this->storageOptionName = $storageOptionName;
            $this->removeNoticeUrlFlagName = $removeNoticeUrlFlagName;
        }

        public function add(WBS_Upgrade_Notice $notice)
        {
            $data = $this->load();

            $data['last_id']++;
            $data['notices'][$data['last_id']] = $notice;

            $this->save($data);

            return $data['last_id'];
        }

        public function remove($id)
        {
            $data = $this->load();
            if (!isset($data['notices'][$id])) {
                return false;
            }

            unset($data['notices'][$id]);

            $this->save($data);

            return true;
        }

        public function show()
        {
            static $firstRun = true;

            $data = $this->load();

            /** @var WBS_Upgrade_Notice $notice */
            foreach ($data['notices'] as $id => $notice) {
                $hideNoticeUrl = esc_html(
                    WC_Weight_Based_Shipping::edit_profile_url(
                        null,
                        array($this->removeNoticeUrlFlagName => $id)
                    )
                );

                echo '
                    <div class="woowbs-upgrade-notice highlight" style="padding: 1em; border: 1px solid red;">
                        <big>'.
                            $notice->getShortHtml().
                            ' <a class="woowbs-upgrade-notice-switcher" href="#">Less</a>
                        </big>
                        <div class="woowbs-upgrade-notice-long-message">'.
                            $notice->getLongHtml().
                            ' <p><a class="button" href="'.$hideNoticeUrl.'">Don\'t show this message again</a></p>
                        </div>
                    </div>
                ';

                if ($firstRun) {
                    $firstRun = false;
                    echo '
                        <script>
                            jQuery(function($) {
                                var toggleSpeed = 0;
                                var $collapsers = $(".woowbs-upgrade-notice-switcher");

                                $collapsers.click(function() {
                                    var $collapser = $(this);

                                    var $content = $collapser
                                        .closest(".woowbs-upgrade-notice")
                                        .find(".woowbs-upgrade-notice-long-message");

                                    $content.toggle(toggleSpeed, function() {
                                        $collapser.text($content.is(":visible") ? "Less" : "More");
                                    });

                                    return false;
                                });

                                $collapsers.click();
                                toggleSpeed = "fast";
                            });
                        </script>
                    ';
                }
            }
        }

        public function getRemoveNoticeUrlFlagName()
        {
            return $this->removeNoticeUrlFlagName;
        }

        private $storageOptionName;
        private $removeNoticeUrlFlagName;

        private function load()
        {
            $data = get_option($this->storageOptionName, null);

            if (!isset($data)) {
                $data = array(
                    'last_id' => 0,
                    'notices' => array(),
                );
            }

            return $data;
        }

        private function save(array $data)
        {
            update_option($this->storageOptionName, $data);
        }
    }
?>