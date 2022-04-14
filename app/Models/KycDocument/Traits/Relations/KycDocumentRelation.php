<?php

namespace App\Models\KycDocument\Traits\Relations;

use App\Models\Country\Country;
use App\Models\FileUpload\FileUpload;
use App\Models\User\User;

trait KycDocumentRelation
{
    public function documents()
    {
        return $this->belongsToMany(FileUpload::class, 'kyc_documents_file_uploads', 'kyc_document_id', 'file_id');
    }

    public function selfie()
    {
        return $this->belongsTo(FileUpload::class, 'selfie_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


