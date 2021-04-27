<?php
# FloatingTexts
# A new production from Ad5001 generated using ImagicalPlugCreator by Ad5001 (C) 2017

namespace Ad5001\FloatingTexts;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\scheduler\PluginTask;

class SetNameTagVisibleTask extends PluginTask {

    /*
     * Runs when the task runs
     * @param $tick int
     * @return void
     */
    public function onRun($tick) {
        foreach ($this->getOwner()->getServer()->getLevels() as $level) {
            foreach ($level->getEntities() as $entity) {
                if (isset($entity->namedtag->isUsedToFloat)) {
                    $entity->setNameTagAlwaysVisible(true);
                    $entity->setNameTagVisible(true);
                    $entity->setImmobile(true);
                }
            }
        }
    }

}
