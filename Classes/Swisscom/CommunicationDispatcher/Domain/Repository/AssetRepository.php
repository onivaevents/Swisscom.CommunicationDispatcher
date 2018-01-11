<?php
namespace Swisscom\CommunicationDispatcher\Domain\Repository;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Media\Domain\Model\Asset;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class AssetRepository extends Repository
{
    /**
     * @var string
     */
    const ENTITY_CLASSNAME = Asset::class;

    /**
     * @param string $filename
     * @return Asset
     */
    public function findByFilename($filename)
    {
        $query = $this->createQuery();
        $constraint = $query->equals('resource.filename', $filename);

        return $query->matching($constraint)->execute()->getFirst();
    }
}
