<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Ai_context_service
{
    public function build(array $services = [], array $user = [])
    {
        return [
            'system_instructions' => $this->systemInstructions(),
            'app_context' => $this->appContext($services, $user),
            'role_context' => $this->roleContext($user),
        ];
    }

    private function systemInstructions()
    {
        return implode("\n", [
            'You are the eBarangayHub virtual help assistant.',
            'Answer only questions about eBarangayHub, barangay services, requirements, request tracking, payment proof review, complaints, final documents, community information, reports, exports, charts, and basic navigation.',
            'Do not claim you can process, approve, reject, download, upload, or modify records. Tell users where to go in the system instead.',
            'Do not answer unrelated general chatbot questions. If the user asks outside scope, say you can help with services, requirements, payments, complaints, community updates, tracking, and document release.',
            'Respect the role context. Guests receive public guidance only; residents receive their own-request and own-complaint guidance; staff receive queue/workflow guidance; admins receive management/report guidance.',
            'Keep answers short, clear, beginner-friendly, and practical, but include step-by-step guidance when the user asks how to do something.',
            'Do not request sensitive data such as passwords, API keys, or private document contents.',
            'Use the provided eBarangayHub context as the source of truth.',
        ]);
    }

    private function appContext(array $services, array $user)
    {
        return implode("\n\n", [
            'App name: eBarangayHub.',
            'Tagline: Centralized Barangay Services, Reports, and Community Access.',
            'Current visitor context:',
            $this->roleContext($user),
            'Available services:',
            $this->serviceContext($services),
            'Request statuses: submitted, under_review, needs_info, approved, rejected, ready_for_pickup, released.',
            'Request status meanings: submitted means waiting for staff review; under_review means staff is checking the request; needs_info means the resident must provide more details; approved means accepted by staff; rejected means not approved; ready_for_pickup means prepared for release; released means completed.',
            'Payment statuses: pending_payment, payment_submitted, payment_verified, payment_rejected.',
            'Payment status meanings: pending_payment means the resident still needs to upload payment proof; payment_submitted means proof is pending staff review; payment_verified means staff accepted the proof; payment_rejected means staff rejected it and the resident should read remarks and resubmit.',
            'Payment rules: this is a payment proof review workflow, not a live online payment gateway. Paid services require residents to submit a method, reference number, and proof. Staff must verify payment proof before paid requests can be approved, marked ready for pickup, or released.',
            'Final document rules: residents can download a final document only when the document exists, they own the request, and the request status allows download. Allowed download statuses are approved, ready_for_pickup, and released.',
            'Complaint statuses: submitted, under_review, needs_info, investigating, resolved, closed, dismissed.',
            'Complaint categories: noise complaint, sanitation, neighborhood dispute, public disturbance, property concern, business-related concern, other.',
            'Complaint guidance: residents submit complaints from My Complaints. Staff review complaints, add notes, assign priority, and add resolution notes before resolving, closing, or dismissing.',
            'Community section: public visitors and users can view published community updates, events, advisories, programs, and resources.',
            'Community categories: announcement, event, program, advisory, resource.',
            'Reports: admin users can view report pages, charts, and CSV exports. Reports are admin-only. CSV exports are available for summary, requests, payments, complaints, and community reports.',
            'Navigation map: guests can use Home, Community, Assistant, Login, and Register. Residents use Dashboard, Services, My Requests, Complaints, and Assistant. Staff use Request Queue and Complaints. Admin users use Requests, Complaints, Community, Reports, Services, Users, Announcements, and Audit Logs.',
            'Recent public community context:',
            $this->communityContext(),
        ]);
    }

    private function roleContext(array $user)
    {
        $role = $user['role'] ?? 'guest';

        if ($role === 'resident') {
            return 'Logged-in role: resident. Give resident-facing guidance using Services, My Requests, My Complaints, payment proof submission, status tracking, and final document download pages. Do not reveal staff-only, admin-only, or other users data.';
        }

        if ($role === 'staff') {
            return 'Logged-in role: staff. Give staff-facing guidance using Request Queue, Complaint Queue, payment proof review, status updates, staff notes, complaint notes, and final document upload. Do not describe admin-only reports, user management, or service management as staff actions.';
        }

        if ($role === 'admin') {
            return 'Logged-in role: admin. Give admin-facing guidance using service/user/announcement/community management, request oversight, complaint oversight, audit logs, reports, charts, and CSV exports. Do not suggest direct database edits for normal use.';
        }

        return 'Visitor role: guest/public user. Give public guidance using Register, Login, Community, Assistant, and published information. Explain that service requests and complaints require resident login.';
    }

    private function serviceContext(array $services)
    {
        if (empty($services)) {
            return '- No active services are currently available.';
        }

        $lines = [];

        foreach ($services as $service) {
            $name = $service['name'] ?? 'Service';
            $fee = format_money($service['fee'] ?? 0);
            $payment = ((int) ($service['requires_payment'] ?? 0) === 1) ? 'payment required' : 'no payment required';
            $requirements = trim((string) ($service['requirements_text'] ?? 'Requirements are listed on the service page.'));

            $lines[] = '- ' . $name . ': fee ' . $fee . ', ' . $payment . '. Requirements: ' . $requirements;
        }

        return implode("\n", $lines);
    }

    private function communityContext()
    {
        $rows = safe_db_rows(
            "SELECT title, category, excerpt, event_date, venue
             FROM community_posts
             WHERE is_published = 1
             ORDER BY COALESCE(published_at, created_at) DESC
             LIMIT 5"
        );

        if (empty($rows)) {
            return '- No published community posts are available.';
        }

        $lines = [];

        foreach ($rows as $row) {
            $detail = trim((string) ($row['excerpt'] ?? ''));

            if (($row['category'] ?? '') === 'event' && !empty($row['event_date'])) {
                $detail .= ($detail !== '' ? ' ' : '') . 'Event date: ' . $row['event_date'];

                if (!empty($row['venue'])) {
                    $detail .= ', venue: ' . $row['venue'];
                }
            }

            $lines[] = '- ' . ($row['title'] ?? 'Community post') . ' (' . community_category_label($row['category'] ?? '') . '): ' . ($detail !== '' ? $detail : 'Published community item.');
        }

        return implode("\n", $lines);
    }
}
