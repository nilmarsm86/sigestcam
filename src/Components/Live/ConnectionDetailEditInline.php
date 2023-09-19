<?php

namespace App\Components\Live;

use App\Components\Live\Traits\ComponentDetailEditInline;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
class ConnectionDetailEditInline
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentDetailEditInline;

    const SAVE_COMMUTATOR = Commutator::class.'_save';
    const SAVE_CAMERA = Camera::class.'_save';
    const SAVE_MODEM = Modem::class.'_save';
    const SHOW_SECURE = self::class.'_show_secure';

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp]
    public ?bool $last = false;

    public function mount(string $entity, string $template)
    {
        $this->entity = $entity;
        $this->template = $template;
    }

    /**
     * Get form success event name
     * @return string
     */
    protected function getSaveEventName(): string
    {
        return $this->entity.'_save_'.$this->connection->name;
    }

    /**
     * Get show secure event name
     * @return string
     */
    protected function getShowSecureEventName(): string
    {
        return static::SHOW_SECURE;
    }

    /**
     * @param mixed $entity
     * @return string|null
     */
    protected function validateUniqueIp(mixed $entity): ?string
    {
        //este codigo se podria convertir en un validador
        if ($this->setter === 'setIp') {
            $existing = $this->entityManager->getRepository($this->entity)->findBy(['ip' => $this->data]);
            if ($existing && count($existing) > 1) {
                return 'Ya existe un equipo con este IP (' . $this->data . ')';
            } else {
                if (isset($existing[0]) && $existing[0]->getId() !== $entity->getId()) {
                    return 'Ya existe un equipo con este IP (' . $this->data . ')';
                }
            }
        }

        return null;
    }

    /**
     * @param ValidatorInterface $validator
     * @param mixed $entity
     * @return array
     */
    protected function validate(ValidatorInterface $validator, mixed $entity): array
    {
        $constraints = [];
        foreach ($this->constraints as $constraint) {
            $constraints[] = new ('Symfony\\Component\\Validator\\Constraints\\' . $constraint['class'])(options: $constraint['options']);
        }

        $errors = $validator->validate($this->data, $constraints);
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }

        $validateIp = $this->validateUniqueIp($entity);
        if(!is_null($validateIp)){
            $messages[] = $validateIp;
        }
        return $messages;
    }

}