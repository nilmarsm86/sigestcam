<?php

namespace App\Components\Live\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait ComponentDetailEditInline
{
    #[LiveProp]
    public ?string $entity = null;

    #[LiveProp]
    public ?string $template = null;

    #[LiveProp]
    public ?string $label = null;

    #[LiveProp(writable: true )]
    public ?string $data = null;

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
    public ?bool $secure = false;

    #[LiveProp]
    public ?bool $refreshTable = true;

    #[LiveProp]
    public ?bool $textarea = false;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[LiveAction]
    public function save(ValidatorInterface $validator, #[LiveArg] mixed $entity): void
    {
        $entity = $this->entityManager->find($this->entity, $entity);

        $this->errors = $this->validate($validator, $entity);

        if (count($this->errors) === 0) {
            call_user_func_array([$entity, $this->setter], [$this->data]);
            try{
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                $this->isEditing = false;
                if($this->refreshTable){
                    $this->emit($this->getSaveEventName());
                }

                if($this->secure){
                    //esto se lanza por el bug que hay con el popup despues que se edita la contraseña que al dar click no se abre el popup
                    $this->dispatchBrowserEvent($this->getShowSecureEventName(), [
                        'data' => $this->data
                    ]);
                }
            }catch (Exception $exception){
                $this->errors[] = $exception->getMessage();
            }
        }
    }

    #[LiveAction]
    public function activateEditing(): void
    {
        $this->isEditing = true;
    }

    #[LiveAction]
    public function showSecure(): void
    {
        $this->dispatchBrowserEvent($this->getShowSecureEventName(), [
            'data' => $this->data
        ]);
    }

    /**
     * @param ValidatorInterface $validator
     * @param mixed $entity
     * @return array
     */
    private function validate(ValidatorInterface $validator, mixed $entity): array
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

    /**
     * Get form success event name
     * @return string
     */
    private function getSaveEventName(): string
    {
        return ':save';
    }

    /**
     * Get show secure event name
     * @return string
     */
    private function getShowSecureEventName(): string
    {
        return ':show_secure';
    }

}