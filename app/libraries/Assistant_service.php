<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Assistant_service
{
    public function answer($question, array $services = [])
    {
        $question = trim((string) $question);
        $normalized = $this->normalize($question);

        if ($question === '') {
            return $this->response(
                'general',
                'Ask me about barangay services, requirements, payments, request statuses, tracking, or final document downloads.',
                $this->defaultSuggestions()
            );
        }

        $service = $this->detectService($normalized, $services);

        if (!empty($service)) {
            return $this->serviceResponse($normalized, $service);
        }

        if ($this->hasAny($normalized, ['requirement', 'requirements', 'document', 'documents', 'attachment', 'attachments', 'valid id', 'proof of residency'])) {
            return $this->response(
                'requirements',
                'Most requests need a valid ID, proof of residency, and supporting files related to your purpose. Open the service page to see the exact requirements before submitting.',
                ['Barangay Clearance requirements', 'Certificate of Residency requirements', 'How do I upload attachments?']
            );
        }

        if ($this->hasAny($normalized, ['payment', 'pay', 'fee', 'gcash', 'maya', 'cash', 'reference', 'proof', 'rejected payment', 'pending payment'])) {
            return $this->paymentResponse($normalized);
        }

        if ($this->hasAny($normalized, ['status', 'submitted', 'under review', 'needs info', 'approved', 'rejected', 'ready for pickup', 'released'])) {
            return $this->statusResponse($normalized);
        }

        if ($this->hasAny($normalized, ['complaint', 'complaints', 'evidence', 'incident', 'respondent', 'resolution', 'investigating', 'dismissed'])) {
            return $this->complaintResponse($normalized);
        }

        if ($this->hasAny($normalized, ['download', 'final document', 'document ready', 'no download', 'download button'])) {
            return $this->response(
                'final_document',
                'Final documents can be downloaded from the request details page after staff uploads the file and the request status is approved, ready for pickup, or released. If there is no download button yet, the document may not be uploaded or the request is not at an allowed status.',
                ['Why is there no download button yet?', 'What does approved mean?', 'How do I track my request?']
            );
        }

        if ($this->hasAny($normalized, ['track', 'tracking', 'my request', 'history', 'reference number', 'where is my request'])) {
            return $this->response(
                'tracking',
                'Log in as a resident, open My Requests, then choose View Details. You can check the reference number, status, staff notes, payment status, attachments, and final document availability there.',
                ['What does under review mean?', 'What do I do after submission?', 'Why payment is pending?']
            );
        }

        if ($this->hasAny($normalized, ['register', 'account', 'login', 'sign up', 'create account'])) {
            return $this->response(
                'account',
                'Residents can register from the public Register page. After logging in, use Services to choose a barangay service and submit a request.',
                ['How do I submit a request?', 'What service should I choose?', 'Where are announcements?']
            );
        }

        if ($this->hasAny($normalized, ['announcement', 'announcements', 'homepage', 'public', 'community', 'event', 'advisory', 'program', 'resource'])) {
            return $this->response(
                'community',
                'Use the Community page to read published barangay announcements, events, advisories, programs, and resources. Admin users manage community posts from Admin Community.',
                ['Where are announcements?', 'How do I register?', 'What services are available?']
            );
        }

        if ($this->hasAny($normalized, ['report', 'reports', 'chart', 'charts', 'csv', 'export', 'audit log', 'audit logs'])) {
            return $this->response(
                'reports',
                'Reports, charts, CSV exports, and audit logs are admin-only tools. Admin users can open Reports from the admin dashboard to review request, payment, complaint, and community summary data.',
                ['How do CSV exports work?', 'Where can admins view reports?', 'What are audit logs for?']
            );
        }

        if ($this->hasAny($normalized, ['staff', 'admin', 'verify', 'process', 'review'])) {
            return $this->response(
                'staff_admin',
                'Staff can review resident requests, open attachments, verify payment proof, update statuses, add notes, and upload final documents. Admin users manage services, users, announcements, audit logs, and request document review.',
                ['How does payment proof review work?', 'What does ready for pickup mean?', 'How are final documents uploaded?']
            );
        }

        if ($this->hasAny($normalized, ['what is this', 'system', 'barangay system', 'help', 'assistant'])) {
            return $this->response(
                'general',
                'eBarangayHub helps residents submit barangay service requests, upload requirements, track status updates, submit payment proof for paid services, file complaints, view community updates, and download approved final documents.',
                $this->defaultSuggestions()
            );
        }

        return $this->response(
            'fallback',
            'I can help with eBarangayHub services, requirements, request tracking, payment proof review, complaints, final documents, community updates, reports, and navigation. Try asking about a specific service, status, payment step, complaint step, or report page.',
            $this->defaultSuggestions()
        );
    }

    public function welcomeMessage()
    {
        return 'Hello. I am the eBarangayHub virtual help assistant. Ask me about services, requirements, payment proof review, request statuses, complaints, tracking, community updates, or document downloads.';
    }

    public function defaultSuggestions()
    {
        return [
            'How do I request Barangay Clearance?',
            'What does Under Review mean?',
            'How does payment proof review work?',
            'How do complaints work?',
        ];
    }

    private function complaintResponse($normalized)
    {
        if ($this->hasAny($normalized, ['resolved', 'closed', 'dismissed'])) {
            return $this->response(
                'complaints',
                'Resolved, Closed, and Dismissed are ending complaint states. Staff should add resolution notes before using these statuses, and residents can review notes from their complaint details page.',
                ['How do I file a complaint?', 'What does investigating mean?', 'Where do I see resolution notes?']
            );
        }

        if ($this->hasAny($normalized, ['investigating', 'under review', 'needs info'])) {
            return $this->response(
                'complaints',
                'Complaint review usually moves from Submitted to Under Review, Needs Info, or Investigating. If staff needs more details, residents should check My Complaints and read the staff notes.',
                ['How do I file a complaint?', 'What evidence can I upload?', 'What does resolved mean?']
            );
        }

        return $this->response(
            'complaints',
            'Residents can file complaints from My Complaints, add the subject, category, incident details, location, respondent name if known, and evidence files. Staff then reviews the complaint, updates status and priority, and adds notes or resolution details.',
            ['What complaint categories are available?', 'What evidence can I upload?', 'How do I track my complaint?']
        );
    }

    private function serviceResponse($normalized, array $service)
    {
        $name = $service['name'];
        $fee = format_money($service['fee'] ?? 0);
        $requires_payment = ((int) ($service['requires_payment'] ?? 0) === 1);

        if ($this->hasAny($normalized, ['requirement', 'requirements', 'document', 'documents', 'attachment', 'attachments'])) {
            return $this->response(
                'service_requirements',
                $name . ' requirements: ' . $service['requirements_text'] . ' Upload clear copies on the request form.',
                ['How much is ' . $name . '?', 'How do I submit a request?', 'How do I track my request?']
            );
        }

        if ($this->hasAny($normalized, ['fee', 'payment', 'pay', 'cost', 'how much'])) {
            $payment_text = $requires_payment
                ? $name . ' has a listed fee of ' . $fee . '. Submit the request first, then upload payment proof on the request details page.'
                : $name . ' is listed as free in the current services setup.';

            return $this->response(
                'service_payment',
                $payment_text,
                [$name . ' requirements', 'How does payment proof review work?', 'What happens after payment proof is verified?']
            );
        }

        return $this->response(
            'service_info',
            $name . ': ' . $service['description'] . ' Requirements: ' . $service['requirements_text'] . ' Fee: ' . $fee . '. Payment: ' . ($requires_payment ? 'payment proof is required for staff review.' : 'not required.'),
            [$name . ' requirements', 'How do I submit a request?', 'How do I track my request?']
        );
    }

    private function paymentResponse($normalized)
    {
        if ($this->hasAny($normalized, ['rejected'])) {
            return $this->response(
                'payment',
                'If payment proof is rejected, open the request details page, read the staff remarks, then resubmit the payment method, reference number, and proof file.',
                ['What proof files are allowed?', 'How do I track my request?', 'What does pending review mean?']
            );
        }

        if ($this->hasAny($normalized, ['pending'])) {
            return $this->response(
                'payment',
                'Awaiting payment proof means the request is for a paid service and no verified payment proof is recorded yet. Submit proof from the request details page.',
                ['How do I submit payment proof?', 'What payment methods are available?', 'Why cannot staff approve my request?']
            );
        }

        return $this->response(
            'payment',
            'For paid services, residents choose GCash, Maya, or Cash, enter a reference number, and upload JPG, PNG, or PDF payment proof. Staff then verifies or rejects the proof before the request can continue.',
            ['Why is payment proof pending?', 'What if payment proof is rejected?', 'Which services have fees?']
        );
    }

    private function statusResponse($normalized)
    {
        $status_text = [
            'submitted' => 'Submitted means the resident sent the request and it is waiting for staff review.',
            'under review' => 'Under Review means staff is checking the request details, attachments, and payment if required.',
            'needs info' => 'Needs Info means staff needs more details or corrected requirements from the resident.',
            'approved' => 'Approved means staff accepted the request. A final document may still need to be uploaded.',
            'rejected' => 'Rejected means the request was not approved. Check staff notes for the reason.',
            'ready for pickup' => 'Ready for Pickup means the request is prepared for release or pickup at the barangay.',
            'released' => 'Released means the request has been completed and released.',
        ];

        foreach ($status_text as $keyword => $answer) {
            if ($this->hasAny($normalized, [$keyword])) {
                return $this->response(
                    'status',
                    $answer,
                    ['How do I track my request?', 'When can I download my document?', 'How does payment proof review work?']
                );
            }
        }

        return $this->response(
            'status',
            'Request statuses are Submitted, Under Review, Needs Info, Approved, Rejected, Ready for Pickup, and Released. Open request details to see the current status and staff notes.',
            ['What does Under Review mean?', 'What does Needs Info mean?', 'What does Ready for Pickup mean?']
        );
    }

    private function detectService($normalized, array $services)
    {
        foreach ($services as $service) {
            $name = $this->normalize($service['name'] ?? '');
            $slug = $this->normalize(str_replace('-', ' ', $service['slug'] ?? ''));

            if ($name !== '' && strpos($normalized, $name) !== false) {
                return $service;
            }

            if ($slug !== '' && strpos($normalized, $slug) !== false) {
                return $service;
            }
        }

        $aliases = [
            'clearance' => 'barangay-clearance',
            'residency' => 'certificate-of-residency',
            'resident certificate' => 'certificate-of-residency',
            'indigency' => 'certificate-of-indigency',
            'business' => 'business-clearance',
        ];

        foreach ($aliases as $keyword => $slug) {
            if (strpos($normalized, $keyword) !== false) {
                foreach ($services as $service) {
                    if (($service['slug'] ?? '') === $slug) {
                        return $service;
                    }
                }
            }
        }

        return null;
    }

    private function response($category, $reply, array $suggestions)
    {
        return [
            'category' => $category,
            'reply' => $reply,
            'suggestions' => $suggestions,
        ];
    }

    private function normalize($value)
    {
        $value = strtolower((string) $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }

    private function hasAny($text, array $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}
