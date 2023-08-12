<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Repository\CommutatorRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_commutator/commutator_detail_edit_inline.html.twig', csrf: false)]
class CommutatorDetailEditInline
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    const SAVE = 'commutator_detail_edit_inline:save';

    #[LiveProp]
    public ?string $label = null;

    #[LiveProp(writable: true )]
    public ?string $data = null;

    #[LiveProp]
    public ?bool $last = false;

    #[LiveProp]
    public ?int $id = null;

    #[LiveProp]
    public bool $isEditing = false;

    #[LiveProp(writable: true)]
    public ?array $errors = null;

    #[LiveProp]
    public array $constraints = [];

    #[LiveProp]
    public bool $editable = true;

    #[LiveProp]
    public ?string $setter = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveAction]
    public function save(
        CommutatorRepository $commutatorRepository,
        ValidatorInterface $validator,
        #[LiveArg] Commutator $commutator
    ): void
    {
        $this->errors = $this->validate($validator);
        $this->validateUniqueIp($commutatorRepository, $commutator);

        if (count($this->errors) === 0) {
            call_user_func_array([$commutator, $this->setter], [$this->data]);
            try{
                $commutatorRepository->save($commutator, true);
                $this->isEditing = false;
                $this->emit(static::SAVE.':'.$this->connection->name);

            }catch (\Exception $exception){
                $this->errors[] = $exception->getMessage();
            }
        }
    }

    #[LiveAction]
    public function activateEditing(): void
    {
        $this->isEditing = true;
    }

    /**
     * @param CommutatorRepository $commutatorRepository
     * @param Commutator $commutator
     * @return void
     */
    private function validateUniqueIp(CommutatorRepository $commutatorRepository, Commutator $commutator): void
    {
        //este codigo se podria convertir en un validador
        if ($this->setter === 'setIp') {
            $existing = $commutatorRepository->findBy(['ip' => $this->data]);
            if ($existing && count($existing) > 1) {
                $this->errors[] = 'Ya existe un Switch con este IP (' . $this->data . ')';
            } else {
                if (isset($existing[0]) && $existing[0]->getId() !== $commutator->getId()) {
                    $this->errors[] = 'Ya existe un Switch con este IP (' . $this->data . ')';
                }
            }
        }
    }

    /**
     * @param ValidatorInterface $validator
     * @return array
     */
    private function validate(ValidatorInterface $validator): array
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
        return $messages;
    }

}