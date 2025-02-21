<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Validator\Constraints;

use App\Entity\Timesheet as TimesheetEntity;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class TimesheetExportedValidator extends ConstraintValidator
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param TimesheetEntity $timesheet
     * @param Constraint $constraint
     */
    public function validate($timesheet, Constraint $constraint)
    {
        if (!($constraint instanceof TimesheetExported)) {
            throw new UnexpectedTypeException($constraint, TimesheetExported::class);
        }

        if (!\is_object($timesheet) || !($timesheet instanceof TimesheetEntity)) {
            throw new UnexpectedTypeException($timesheet, TimesheetEntity::class);
        }

        if (!$timesheet->isExported()) {
            return;
        }

        if (null !== $this->security->getUser() && $this->security->isGranted('edit_exported_timesheet')) {
            return;
        }

        $this->context->buildViolation(TimesheetExported::getErrorName(TimesheetExported::TIMESHEET_EXPORTED))
            ->atPath('exported')
            ->setTranslationDomain('validators')
            ->setCode(TimesheetExported::TIMESHEET_EXPORTED)
            ->addViolation();
    }
}
