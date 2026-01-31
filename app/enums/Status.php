<?php

namespace App\enums;

enum Status: string
{
    case Draft = 'Draft';

    case Published = 'Published';

    case Archived = 'Archived';
}

