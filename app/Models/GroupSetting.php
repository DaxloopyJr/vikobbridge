<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id', 'key', 'value', 'type', 'label', 'description',
    ];

    /**
     * Get a typed value for this setting
     */
    public function typedValue(): mixed
    {
        return match($this->type) {
            'integer' => (int) $this->value,
            'decimal' => (float) $this->value,
            'boolean' => (bool) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Get a setting by key for a group
     */
    public static function getValue(int $groupId, string $key, mixed $default = null): mixed
    {
        $setting = self::where('group_id', $groupId)->where('key', $key)->first();
        return $setting ? $setting->typedValue() : $default;
    }

    /**
     * Set a setting value for a group
     */
    public static function setValue(int $groupId, string $key, mixed $value, string $type = 'string', ?string $label = null, ?string $description = null): self
    {
        $stringValue = match($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        return self::updateOrCreate(
            ['group_id' => $groupId, 'key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
                'label' => $label,
                'description' => $description,
            ]
        );
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
