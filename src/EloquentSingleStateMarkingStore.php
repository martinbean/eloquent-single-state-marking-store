<?php

namespace MartinBean\SymfonyWorkflow;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class EloquentSingleStateMarkingStore implements MarkingStoreInterface
{
    /**
     * The attribute to get and set on the Eloquent model.
     *
     * @var string
     */
    private $attribute;

    /**
     * Create a new marking store instance.
     *
     * @param  string  $attribute
     * @return void
     */
    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getMarking($subject)
    {
        $this->assertSubjectIsEloquentModel($subject);

        $status = $subject->getAttribute($this->attribute);

        return new Marking([
            $status => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setMarking($subject, Marking $marking)
    {
        $this->assertSubjectIsEloquentModel($subject);

        $subject->setAttribute($this->attribute, key($marking->getPlaces()));

        $subject->save();
    }

    /**
     * Determine if the given subject is an Eloquent model.
     *
     * @param  mixed  $subject
     * @return void
     *
     * @throws \RuntimeException
     */
    private function assertSubjectIsEloquentModel($subject)
    {
        if ($subject instanceof Model) {
            return;
        }

        throw new RuntimeException('Subject is not an Eloquent model');
    }
}
