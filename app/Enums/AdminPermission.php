<?php

namespace App\Enums;

enum AdminPermission: string
{
    // Usuarios
    case USER_ACCOUNTS_ENROLLMENT = 'users.accounts.enrollment';
    case USER_ACCOUNTS_EDIT = 'users.accounts.edit';
    case USER_ACCOUNTS_ADD = 'users.accounts.add';
    case USER_DOCUMENT_DELIVERY = 'users.document_delivery';
    case USER_RE_ENROLLMENT = 'users.re_enrollment';
    case USER_TEACHERS = 'users.teachers.enter';
    case USER_TEACHERS_EDIT = 'users.teachers.edit';
    case USER_TEACHERS_ADD = 'users.teachers.add';
    case USER_ADMINISTRATIVE = 'users.administrative';
    case USER_PARENT_USERS = 'users.parent_users';
    case USER_RENAME_STUDENTS = 'users.rename_students';
    case USER_MEMOS_DEMERITS = 'users.memos_demerits';
    case USER_WITHDRAWAL_SCREEN = 'users.withdrawal_screen';
    case USER_NURSE_OFFICE = 'users.nurse_office';
    case USER_NURSE_REPORTS = 'users.nurse_reports';
    case USER_PAYPAL = 'users.paypal';

        // Mensajes
    case MESSAGES_SEND_EMAIL = 'messages.send_email';
    case MESSAGES_SEND_SMS = 'messages.send_sms';
    case MESSAGES_CREATE_MESSAGES = 'messages.create_messages';
    case MESSAGES_MY_MESSAGES = 'messages.my_messages';
    case MESSAGES_OTHER_MESSAGES = 'messages.other_messages';
    case MESSAGES_SENT_MESSAGES = 'messages.sent_messages';

        // Acceso
    case ACCESS_SCHOOL_YEAR = 'access.school_year';
    case ACCESS_CHANGE_DATES = 'access.change_dates';
    case ACCESS_EXPORT_DATA = 'access.export_data';
    case ACCESS_IMPORT_DATA = 'access.import_data';
    case ACCESS_DOCUMENTS = 'access.documents';
    case ACCESS_CHANGE_INITIAL_MESSAGE = 'access.change_initial_message';
    case ACCESS_SEND_GROUP_MESSAGES = 'access.send_group_messages';
    case ACCESS_DEACTIVATE_MESSAGE = 'access.deactivate_message';
    case ACCESS_ACTIVATIONS = 'access.activations';
    case ACCESS_SURVEYS = 'access.surveys';
    case ACCESS_REQUIREMENTS = 'access.requirements';
    case ACCESS_FORCE_PARENT_PASSWORD_CHANGE = 'access.force_change_parent_passwords';
    case ACCESS_RE_ENROLLMENT = 'access.re_enrollment';

        // Utilidades
    case UTILITIES_BACKUP = 'utilities.backup';
    case UTILITIES_TRANSFER_DATA = 'utilities.transfer_data';
    case UTILITIES_UTILITY_EXPORT_DATA = 'utilities.export_data';

        // Notas
    case GRADES_GRADES_OPTIONS = 'grades.grades_options';
    case GRADES_CATALOG = 'grades.catalog';
    case GRADES_GRADE_BY_GRADE = 'grades.grade_by_grade';
    case GRADES_CREATE_GRADES = 'grades.create_grades';
    case GRADES_SPECIAL_PROGRAMS = 'grades.special_programs';
    case GRADES_DELETE_STUDENT_COURSES = 'grades.delete_student_courses';
    case GRADES_ATTENDANCE_OPTIONS = 'grades.attendance_options';
    case GRADES_ATTENDANCE_ENTRY = 'grades.attendance_entry';
    case GRADES_ORDER_COURSES = 'grades.order_courses';
    case GRADES_DAILY_CLASSES = 'grades.daily_classes';
    case GRADES_WORK_PLAN = 'grades.work_plan';
    case GRADES_MESSAGES_FOR_GRADES = 'grades.messages_for_grades';
    case GRADES_SUMMER_CLASSES = 'grades.summer_classes';
    case GRADES_CUMULATIVE_CARD = 'grades.cumulative_card';
    case GRADES_PRINT_TRANSCRIPT = 'grades.print_transcript';

        // Entrada de Códigos
    case CODES_WITHDRAWAL = 'codes.withdrawal';
    case CODES_SOCIO_ECONOMIC = 'codes.socio_economic';
    case CODES_SPECIAL = 'codes.special';
    case CODES_ENTER_DESCRIPTION = 'codes.enter_description';
    case CODES_DEPARTMENTS = 'codes.departments';

        // Informes de Maestros
    case ACCESS_REPORTS_POSTAL_ADDRESS = 'access_reports.postal_address';
    case ACCESS_REPORTS_RELIGION = 'access_reports.religion';
    case ACCESS_REPORTS_USERS_AND_PASSWORDS = 'access_reports.users_and_passwords';
    case ACCESS_REPORTS_CERTIFIED_LETTER = 'access_reports.certified_letter';
    case ACCESS_REPORTS_TEACHERS_LIST = 'access_reports.teachers_list';
    case ACCESS_REPORTS_TEACHERS_EMAIL = 'access_reports.teachers_email';
    case ACCESS_REPORTS_TEACHERS_PHONE = 'access_reports.teachers_phone';
    case ACCESS_REPORTS_LEVELS_LIST = 'access_reports.levels_list';
    case ACCESS_REPORTS_HOMEROOM_TEACHERS = 'access_reports.homeroom_teachers';
    case ACCESS_REPORTS_TEACHERS_SIGNATURE_LIST = 'access_reports.teachers_signature_list';
    case ACCESS_REPORTS_TEACHERS_ADDRESS = 'access_reports.teachers_address';
    case ACCESS_REPORTS_TEACHERS_PREPARATION = 'access_reports.teachers_preparation';
    case ACCESS_REPORTS_TEACHERS_CLUB_LIST = 'access_reports.teachers_club_list';
    case ACCESS_REPORTS_SOCIO_ECONOMIC = 'access_reports.socio_economic';
    case ACCESS_REPORTS_TEACHERS_LICENSE = 'access_reports.teachers_license';
    case ACCESS_REPORTS_NON_TEACHING_STAFF = 'access_reports.non_teaching_staff';

        // Informes de Acceso
    case ACCESS_REPORTS_STUDENTS_LIST = 'access_reports.students_list';
    case ACCESS_REPORTS_HOMEROOM = 'access_reports.homeroom';
    case ACCESS_REPORTS_TOTALS_BY_GRADE = 'access_reports.totals_by_grade';
    case ACCESS_REPORTS_SIGNATURE_LIST = 'access_reports.signature_list';
    case ACCESS_REPORTS_USERS_LIST = 'access_reports.users_list';
    case ACCESS_REPORTS_RE_ENROLLMENT_LIST = 'access_reports.re_enrollment_list';
    case ACCESS_REPORTS_DAILY_ATTENDANCE = 'access_reports.daily_attendance';
    case ACCESS_REPORTS_ACCESSED_ACCOUNTS = 'access_reports.accessed_accounts';
    case ACCESS_REPORTS_SURVEY = 'access_reports.survey';
    case ACCESS_REPORTS_PARENT_ACCOUNTS = 'access_reports.parent_accounts';
    case ACCESS_REPORTS_INCOMPLETE_ACCOUNTS = 'access_reports.incomplete_accounts';
    case ACCESS_REPORTS_LABEL = 'access_reports.label';
    case ACCESS_REPORTS_FAMILY_GRADE = 'access_reports.family_grade';
    case ACCESS_REPORTS_ACCOUNTS_LIST = 'access_reports.accounts_list';
    case ACCESS_REPORTS_ENROLLMENT_FORM = 'access_reports.enrollment_form';
    case ACCESS_REPORTS_NEW_STUDENTS = 'access_reports.new_students';
    case ACCESS_REPORTS_DISCOUNTS_LIST = 'access_reports.discounts_list';
    case ACCESS_REPORTS_MEDICATIONS = 'access_reports.medications';
    case ACCESS_REPORTS_DROPOUT_LIST = 'access_reports.dropout_list';
    case ACCESS_REPORTS_CONDITIONS_ALLERGIES = 'access_reports.conditions_allergies';
    case ACCESS_REPORTS_PHONE_LIST = 'access_reports.phone_list';
    case ACCESS_REPORTS_BIRTHDAYS_LIST = 'access_reports.birthdays_list';
    case ACCESS_REPORTS_ENROLLMENT_BY_CLASS = 'access_reports.enrollment_by_class';
    case ACCESS_REPORTS_EMAIL_LIST = 'access_reports.email_list';
    case ACCESS_REPORTS_PARENTS_LIST = 'access_reports.parents_list';
    case ACCESS_REPORTS_PARENT_WORK_LIST = 'access_reports.parent_work_list';

        // Cuentas a Cobrar
    case ACCOUNTS_RECEIVABLE_ENTER_PAYMENTS = 'accounts_receivable.enter_payments.enter';
    case ACCOUNTS_RECEIVABLE_ENTER_PAYMENTS_ADD = 'accounts_receivable.enter_payments.add';
    case ACCOUNTS_RECEIVABLE_ENTER_PAYMENTS_DELETE = 'accounts_receivable.enter_payments.delete';
    case ACCOUNTS_RECEIVABLE_ENTER_PAYMENTS_CHANGE = 'accounts_receivable.enter_payments.change';
    case ACCOUNTS_RECEIVABLE_BUDGET = 'accounts_receivable.budget';
    case ACCOUNTS_RECEIVABLE_COSTS = 'accounts_receivable.costs';
    case ACCOUNTS_RECEIVABLE_CREATE_CHARGES = 'accounts_receivable.create_charges';
    case ACCOUNTS_RECEIVABLE_VIEW_PAYMENTS = 'accounts_receivable.view_payments';
    case ACCOUNTS_RECEIVABLE_SURCHARGES = 'accounts_receivable.surcharges';
    case ACCOUNTS_RECEIVABLE_DAILY_PAYMENTS = 'accounts_receivable.daily_payments';
    case ACCOUNTS_RECEIVABLE_ACCOUNT_STATEMENT = 'accounts_receivable.account_statement';
    case ACCOUNTS_RECEIVABLE_DEBTORS_LIST = 'accounts_receivable.debtors_list';
    case ACCOUNTS_RECEIVABLE_30_60_90 = 'accounts_receivable.30_60_90';
    case ACCOUNTS_RECEIVABLE_COLLECTION_LETTER = 'accounts_receivable.collection_letter';
    case ACCOUNTS_RECEIVABLE_PAYMENT_BOOK = 'accounts_receivable.payment_book';
    case ACCOUNTS_RECEIVABLE_BUDGET_INFO = 'accounts_receivable.budget_info';
    case ACCOUNTS_RECEIVABLE_PAYMENT_INFO = 'accounts_receivable.payment_info';
    case ACCOUNTS_RECEIVABLE_DATE_LIST = 'accounts_receivable.date_list';
    case ACCOUNTS_RECEIVABLE_PAYMENT_LIST = 'accounts_receivable.payment_list';

        // Informes de Notas
    case GRADES_REPORTS_NOTE_REGISTRY = 'grades_reports.note_registry';
    case GRADES_REPORTS_NOTE_CARD = 'grades_reports.note_card';
    case GRADES_REPORTS_NOTE_DISTRIBUTION = 'grades_reports.note_distribution';
    case GRADES_REPORTS_AVERAGE_LIST = 'grades_reports.average_list';
    case GRADES_REPORTS_FAILED_LIST = 'grades_reports.failed_list';
    case GRADES_REPORTS_PROGRESS_SHEET = 'grades_reports.progress_sheet';
    case GRADES_REPORTS_RANK_LIST = 'grades_reports.rank_list';
    case GRADES_REPORTS_NOTES_IN_LETTERS = 'grades_reports.notes_in_letters';
    case GRADES_REPORTS_GRADE_RANK = 'grades_reports.grade_rank';
    case GRADES_REPORTS_AVERAGE_AND_BEHAVIOR = 'grades_reports.average_and_behavior';
    case GRADES_REPORTS_REGIWEB_CHANGES = 'grades_reports.regiweb_changes';
    case GRADES_REPORTS_SUMMER_REPORT = 'grades_reports.summer_report';
    case GRADES_REPORTS_PERCENT_TO_DECIMAL = 'grades_reports.percent_to_decimal';
    case GRADES_REPORTS_HONOR_ROLL = 'grades_reports.honor_roll';
    case GRADES_REPORTS_DEFICIENCY_REPORTS = 'grades_reports.deficiency_reports';

    public function label(): string
    {
        return __("permissions.admin.{$this->value}");
    }
}
