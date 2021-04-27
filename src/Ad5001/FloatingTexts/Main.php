<?php
# FloatingTexts
# A new production from Ad5001 generated using ImagicalPlugCreator by Ad5001 (C) 2017

namespace Ad5001\FloatingTexts;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\level\LevelLoadEvent
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\nbt\tag\StringTag;

class Main extends PluginBase implements Listener {

	/*
	 * Called when the plugin enables
	 */
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->sessions = [];
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new SetNameTagVisibleTask($this),10);
	}

	/*
	 * Called when one of the defined commands of the plugin has been called
	 * @param $sender CommandSender
	 * @param $cmd    Command
	 * @param $label  mixed
	 * @param $args   array
	 * return bool
	 */
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		switch ($command->getName()) {
			case "createfloat":
				if ($sender instanceof Player) {
					if (isset($args[0])) {
						$text = implode(" ", $args);
						$text = str_ireplace("\\n", "\n", $text);
						$this->sessions[$sender->getName()] = $text;
						$sender->sendMessage("Tap an entity !");
					}
				}
				return true;
				break;
		}
	}

	/*
	 * When a player hits an entity with a session, set his nametag
	 * @param $event EntityDamageEvent
	 */
	public function onEntityDamage(EntityDamageEvent $event) {
		if ($event instanceof EntityDamageByEntityEvent) {
			if ($event->getDamager() instanceof Player && isset($this->sessions[$event->getDamager()->getName()])) {
				$event->getEntity()->addEffect(new EffectInstance(Effect::getEffectByName("invisibility"))->setAmbient(true)->setVisible(false));
				$event->getEntity()->setNameTag($this->sessions[$event->getDamager()->getName()]);
				$event->getEntity()->setNameTagAlwaysVisible(true);
				$event->getEntity()->setNameTagVisible(true);
				$event->getEntity()->setImmobile(true);
				$event->getEntity()->namedtag->isUsedToFloat = new StringTag("isUsedToFloat", "true");
				$event->getEntity()->setNameTag($this->sessions[$event->getDamager()->getName()]);
				$event->setCancelled();
				unset($this->sessions[$event->getDamager()->getName()]);
			} elseif (isset($event->getEntity()->namedtag->isUsedToFloat)) {
				if (!($event->getDamager() instanceof Player && $event->getDamager()->isOp())) {
					$event->setCancelled();
				}
			}
		} elseif (isset($event->getEntity()->namedtag->isUsedToFloat)) {
			$event->setCancelled();
		}
	}


	/*
	 * Checks when a level loads with floats to regive them the flags and effects.
	 * @param $event LevelLoadEvent
	 */
	public function onLevelLoad(LevelLoadEvent $event) {
		foreach ($event->getLevel()->getEntities() as $entity) {
			if (isset($entity->namedtag->isUsedToFloat)) {
				$entity->addEffect(new EffectInstance(Effect::getEffectByName("invisibility"), 99999, 0, false));
				$entity->setNameTagAlwaysVisible(true);
				$entity->setNameTagVisible(true);
				$entity->setImmobile(true);
			}
		}
	}

}
