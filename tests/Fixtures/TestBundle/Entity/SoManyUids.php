<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatform\Tests\Fixtures\TestBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Nonstandard\UuidV6;

/**
 * @ORM\Entity
 * @ApiResource(attributes={
 *     "pagination_partial"=true,
 *     "pagination_via_cursor"={
 *         {"field"="id", "direction"="DESC"}
 *     }
 * })
 *
 * @ApiFilter(RangeFilter::class, properties={"id"})
 * @ApiFilter(OrderFilter::class, properties={"id"="DESC"})
 */
class SoManyUids
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public $id;

    /**
     * @ORM\Column(nullable=true)
     */
    public $content;

    public function __construct($id)
    {
        if ($id) {
            $this->id = UuidV6::fromString($id);
        } else {
            $this->id = UuidV6::uuid6();
        }
    }
}