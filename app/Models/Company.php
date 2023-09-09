<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static find($id)
 * @method where(string $string, $companyId)
 * @method static create($validated_data)
 */
class Company extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $table = "companies";
    protected $fillable = [
        'company_id',
        'tax_code',
        'reg_id',
        'vat_id',
        'name',
        'category',
        'country',
        'place',
        'postal_code',
        'address',
        'phone_num',
        'fax',
        'email',
        'url',
        'logo_url',
        'user_id'
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(GoodsOrServices::class);
    }

    public function findByCompanyId($company_id)
    {
        return $this->where('company_id', $company_id)->first();
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'issuer_company_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bankAccounts(): HasMany // fix not use
    {
        return $this->hasMany(BankAccount::class);
    }

    public static array $rules = [
        'company_id' => 'required|string|max:255',
        'tax_code' => 'required|string|max:255',
        'reg_id' => 'nullable|string|max:255',
        'vat_id' => 'nullable|string|max:255',
        'name' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'place' => 'required|string|max:255',
        'postal_code' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'phone_num' => 'nullable|string|max:255',
        'fax' => 'nullable|string|max:255',
        'email' => 'nullable|string|email|max:255',
        'url' => 'nullable|string|url|max:255',
        'logo_url' => 'nullable|string|url|max:255',
        'user_id' => 'required|uuid'
    ];
}
