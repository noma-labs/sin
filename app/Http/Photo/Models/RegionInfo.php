<?php

declare(strict_types=1);

namespace App\Photo\Models;

final class RegionInfo
{
    /**
     * @param  array<string, int|string>  $AppliedToDimensions
     * @param  RegionInfoRegion[]  $RegionList
     */
    public function __construct(
        public array $AppliedToDimensions,
        public array $RegionList
    ) {}

    /**
     * @param array{
     *   AppliedToDimensions?: array<string, int|string>,
     *   RegionList?: array<int, array<string, mixed>>
     * }|null $data
     */
    public static function fromArray(?array $data): self
    {
        if ($data === null) {
            return new self([], []);
        }
        $regions = [];
        foreach ($data['RegionList'] ?? [] as $region) {
            $regions[] = RegionInfoRegion::fromArray($region);
        }

        return new self(
            $data['AppliedToDimensions'] ?? [],
            $regions
        );
    }

    /**
     * @return array{AppliedToDimensions: array<string, int|string>, RegionList: array<int, array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'AppliedToDimensions' => $this->AppliedToDimensions,
            'RegionList' => array_map(fn (RegionInfoRegion $r): array => $r->toArray(), $this->RegionList),
        ];
    }
}

final class RegionInfoRegion
{
    /**
     * @param  array<string, float>  $Area
     */
    public function __construct(
        public array $Area,
        public string $Name,
        public float|int $Rotation,
        public string $Type
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['Area'] ?? [],
            $data['Name'] ?? '',
            $data['Rotation'] ?? 0,
            $data['Type'] ?? ''
        );
    }

    /**
     * @return array{Area: array<string, float>, Name: string, Rotation: float|int, Type: string}
     */
    public function toArray(): array
    {
        return [
            'Area' => $this->Area,
            'Name' => $this->Name,
            'Rotation' => $this->Rotation,
            'Type' => $this->Type,
        ];
    }
}
