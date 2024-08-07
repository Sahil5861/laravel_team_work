<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name', 'business_email', 'phone_number', 'city', 'state', 'country', 'contact_person_id', 'authenticated',
    ];

    public function contactPerson()
    {
        return $this->belongsTo(ContactPerson::class, 'contact_person_id');
        
    }

    public function setPrimaryContact($contactPersonId)
    {
        // Set all contact persons associated with this dealer to 'no'
        $this->contactPersons()->update(['is_primary' => 'no']);

        // Set the selected contact person to 'yes'
        $this->contactPersons()->where('id', $contactPersonId)->update(['is_primary' => 'yes']);

        return response()->json(['success' => 'Primary contact updated successfully.']);
    }


    protected static function booted()
    {
        static::saved(function ($dealer) {
            // Reset all contact persons for this dealer to is_primary = 0
            ContactPerson::where('id', $dealer->id)->update(['is_primary' => 0]);
            
            // If contact_person_id is set, update the corresponding contact person to is_primary = 1
            if ($dealer->contact_person_id) {
                $contactPerson = ContactPerson::where('id', $dealer->contact_person_id)->first();
                if ($contactPerson) {
                    $contactPerson->is_primary = 1;
                    $contactPerson->save();
                }
            }
        });
    }


}
