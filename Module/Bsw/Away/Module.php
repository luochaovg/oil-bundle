<?php

namespace Leon\BswBundle\Module\Bsw\Away;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\FoundationEntity;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Exception\LogicException;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Exception\RepositoryException;
use BadFunctionCallException;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const BEFORE_AWAY = 'BeforeAway';   // 删除前处理 (事务级)
    const AFTER_AWAY  = 'AfterAway';    // 删除后处理 (事务级)

    /**
     * @return bool
     */
    public function allowAjax(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function allowIframe(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'away';
    }

    /**
     * @return string|null
     */
    public function twig(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    public function css(): ?array
    {
        return null;
    }

    /**
     * @return array
     */
    public function javascript(): ?array
    {
        return null;
    }

    /**
     * @return ArgsInput
     */
    public function input(): ArgsInput
    {
        return new Input();
    }

    /**
     * @return ArgsOutput
     * @throws
     */
    public function logic(): ArgsOutput
    {
        if (empty($this->entity)) {
            throw new ModuleException('Entity is required for away module');
        }

        $result = $this->repository->transactional(
            function () {

                $effect = [];
                $pk = $this->repository->pk();

                /**
                 * Before away
                 */

                $effect[Abs::TAG_TRANS_BEFORE] = $result = $this->caller(
                    $this->method,
                    self::BEFORE_AWAY,
                    [Message::class, Error::class, true],
                    null,
                    [$this->input->id, $this->input->args]
                );

                if ($result instanceof Error) {
                    throw new LogicException($result->tiny());
                }

                if (($result instanceof Message) && $result->isErrorClassify()) {
                    throw new LogicException($result->getMessage());
                }

                /**
                 * Current entity
                 */

                $effect[$this->entity] = $result = $this->repository->away([$pk => $this->input->id]);
                if ($result === false) {
                    throw new RepositoryException($this->repository->pop());
                }

                /**
                 * Relation entity
                 */

                foreach ($this->input->relation as $entity => $relationId) {
                    $class = FoundationEntity::class;
                    if (!Helper::extendClass($entity, $class)) {
                        throw new BadFunctionCallException("Relation `entity` should be instance of `{$class}`");
                    }

                    $repository = $this->web->repo($entity);
                    $effect[$entity] = $result = $repository->away([$relationId => $this->input->id]);
                    if ($result === false) {
                        throw new RepositoryException($repository->pop());
                    }
                }

                /**
                 * After away
                 */

                $effect[Abs::TAG_TRANS_AFTER] = $result = $this->caller(
                    $this->method,
                    self::AFTER_AWAY,
                    [Message::class, Error::class, true],
                    null,
                    [$this->input->id, $this->input->args]
                );

                if ($result instanceof Error) {
                    throw new LogicException($result->tiny());
                }

                if (($result instanceof Message) && $result->isErrorClassify()) {
                    throw new LogicException($result->getMessage());
                }

                return $effect;
            }
        );

        /**
         * Handle error
         */
        if ($result === false) {
            return $this->showError($this->repository->pop());
        }

        $count = count($result) - 2;
        $type = $count > 1 ? 6 : 5;

        $relation = $this->input->relation;
        $relation[$this->entity] = $this->repository->pk();

        $this->web->databaseOperationLogger($this->entity, $type, $relation, $result, ['effect' => $count]);

        return $this->showSuccess($this->input->i18nAway);
    }
}