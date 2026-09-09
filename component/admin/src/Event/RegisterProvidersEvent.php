<?php
namespace xdecaro\Component\Analytics\Administrator\Event;

defined('_JEXEC') or die;

use Joomla\Event\Event;
use xdecaro\Component\Analytics\Administrator\Service\ProviderRegistry;

final class RegisterProvidersEvent extends Event
{
    public const NAME = 'onXdecaroAnalyticsRegisterProviders';

    public function __construct(ProviderRegistry $registry)
    {
        parent::__construct(self::NAME, ['subject' => $registry]);
    }

    public function getRegistry(): ProviderRegistry
    {
        return $this->getArgument('subject');
    }
}
