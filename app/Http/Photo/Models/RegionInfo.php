<?php

declare(strict_types=1);

namespace App\Photo\Models;

final class RegionInfo
{
    public function __construct(
        public array $AppliedToDimensions,
        /** @var RegionInfoRegion[] */
        public array $RegionList
    ) {}

    public static function fromArray(array|null $data): self
    {
        if ($data == null){
            return  new self([],[]);
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

    public function toArray(): array
    {
        return [
            'AppliedToDimensions' => $this->AppliedToDimensions,
            'RegionList' => array_map(fn ($r) => $r->toArray(), $this->RegionList),
        ];
    }
}

final class RegionInfoRegion
{
    public function __construct(public array $Area, public string $Name, public $Rotation, public string $Type) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['Area'] ?? [],
            $data['Name'] ?? '',
            $data['Rotation'] ?? 0,
            $data['Type'] ?? ''
        );
    }

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
