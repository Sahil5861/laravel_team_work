<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

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


}
