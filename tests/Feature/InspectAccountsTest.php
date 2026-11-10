<?php

use App\Models\EarlyLeaveRequest;
use App\Models\PicketSchedule;

test('inspect user accounts and active status', function () {
    $earlyLeave = EarlyLeaveRequest::latest()->first();
    dump($earlyLeave ? [
        'id' => $earlyLeave->id,
        'student_id' => $earlyLeave->student_id,
        'approved_by' => $earlyLeave->approved_by,
        'approver' => $earlyLeave->approver?->toArray(),
        'date' => $earlyLeave->date,
    ] : 'no early leave');

    $allPickets = PicketSchedule::all()->toArray();
    dump(['allPickets' => $allPickets]);

    expect(true)->toBeTrue();
});
