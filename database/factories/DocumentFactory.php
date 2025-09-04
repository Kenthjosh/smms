<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Document;
use App\Models\Application;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentTypes = [
            'transcript_of_records',
            'birth_certificate',
            'certificate_of_enrollment',
            'barangay_clearance',
            'medical_certificate',
            'passport_photo',
            'recommendation_letter',
            'essay_document'
        ];

        $documentType = fake()->randomElement($documentTypes);
        $originalName = $this->getOriginalName($documentType);
        $mimeType = $this->getMimeType($documentType);

        return [
            'application_id' => Application::factory(),
            'document_type' => $documentType,
            'file_path' => 'documents/' . fake()->year() . '/' . fake()->month() . '/' . fake()->uuid() . '.' . $this->getExtension($mimeType),
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => fake()->numberBetween(50000, 2000000), // 50KB to 2MB
            'is_verified' => fake()->boolean(70), // 70% chance of being verified
            'verified_at' => function (array $attributes) {
                return $attributes['is_verified'] ? fake()->dateTimeBetween('-30 days', 'now') : null;
            },
            'verified_by' => function (array $attributes) {
                return $attributes['is_verified'] ? fake()->numberBetween(1, 10) : null; // Assuming user IDs 1-10
            },
            'created_at' => fake()->dateTimeBetween('-60 days', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Document for a specific application
     */
    public function forApplication(Application $application): static
    {
        return $this->state(function (array $attributes) use ($application) {
            // Get scholarship-specific document types
            $documentTypes = $this->getScholarshipDocumentTypes($application->scholarship->slug);

            return [
                'application_id' => $application->id,
                'document_type' => fake()->randomElement($documentTypes),
            ];
        });
    }

    /**
     * Verified document
     */
    public function verified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_verified' => true,
                'verified_at' => fake()->dateTimeBetween($attributes['created_at'], 'now'),
                'verified_by' => fake()->numberBetween(1, 10), // Committee member ID
            ];
        });
    }

    /**
     * Unverified document
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_verified' => false,
                'verified_at' => null,
                'verified_by' => null,
            ];
        });
    }

    /**
     * Academic document (transcript, certificates, etc.)
     */
    public function academic(): static
    {
        return $this->state(function (array $attributes) {
            $academicDocTypes = [
                'transcript_of_records',
                'certificate_of_enrollment',
                'certificate_of_grades',
                'diploma',
                'academic_recommendation_letter'
            ];

            $docType = fake()->randomElement($academicDocTypes);

            return [
                'document_type' => $docType,
                'original_name' => $this->getOriginalName($docType),
                'mime_type' => 'application/pdf',
                'file_size' => fake()->numberBetween(100000, 1000000), // Larger for academic docs
            ];
        });
    }

    /**
     * Identity document (birth cert, IDs, etc.)
     */
    public function identity(): static
    {
        return $this->state(function (array $attributes) {
            $identityDocTypes = [
                'birth_certificate',
                'barangay_clearance',
                'certificate_of_indigency',
                'valid_id',
                'passport_photo'
            ];

            $docType = fake()->randomElement($identityDocTypes);

            return [
                'document_type' => $docType,
                'original_name' => $this->getOriginalName($docType),
                'mime_type' => in_array($docType, ['passport_photo', 'valid_id']) ? 'image/jpeg' : 'application/pdf',
            ];
        });
    }

    /**
     * Medical document
     */
    public function medical(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'document_type' => 'medical_certificate',
                'original_name' => 'medical_certificate_' . fake()->date('Y_m_d') . '.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => fake()->numberBetween(80000, 300000),
            ];
        });
    }

    /**
     * Sports-related document
     */
    public function sports(): static
    {
        return $this->state(function (array $attributes) {
            $sportsDocTypes = [
                'athletic_performance_record',
                'coach_recommendation_letter',
                'sports_participation_certificate',
                'medical_clearance_sports',
                'team_membership_certificate'
            ];

            $docType = fake()->randomElement($sportsDocTypes);

            return [
                'document_type' => $docType,
                'original_name' => $this->getOriginalName($docType),
                'mime_type' => 'application/pdf',
            ];
        });
    }

    /**
     * Get scholarship-specific document types
     */
    private function getScholarshipDocumentTypes(string $scholarshipSlug): array
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => [
                'transcript_of_records',
                'certificate_of_enrollment',
                'birth_certificate',
                'barangay_clearance',
                'passport_photo',
                'academic_recommendation_letter',
                'certificate_of_awards',
                'essay_document'
            ],
            'sports-scholarship' => [
                'transcript_of_records',
                'birth_certificate',
                'barangay_clearance',
                'medical_certificate',
                'passport_photo',
                'athletic_performance_record',
                'coach_recommendation_letter',
                'sports_participation_certificate'
            ],
            'need-based-scholarship' => [
                'transcript_of_records',
                'birth_certificate',
                'barangay_clearance',
                'certificate_of_indigency',
                'family_income_certificate',
                'passport_photo',
                'parent_employment_certificate',
                'utility_bills'
            ],
            'indigenous-scholarship' => [
                'transcript_of_records',
                'birth_certificate',
                'certificate_of_tribal_membership',
                'barangay_clearance',
                'cultural_endorsement_letter',
                'passport_photo',
                'community_recommendation_letter',
                'ancestral_domain_certificate'
            ],
            default => [
                'transcript_of_records',
                'birth_certificate',
                'barangay_clearance',
                'passport_photo'
            ]
        };
    }

    /**
     * Get realistic original filename based on document type
     */
    private function getOriginalName(string $documentType): string
    {
        return match ($documentType) {
            'transcript_of_records' => 'transcript_of_records_' . fake()->date('Y_m_d') . '.pdf',
            'birth_certificate' => 'birth_certificate_' . fake()->lastName() . '.pdf',
            'certificate_of_enrollment' => 'enrollment_certificate_AY' . fake()->year() . '.pdf',
            'barangay_clearance' => 'barangay_clearance_' . fake()->date('Y_m_d') . '.pdf',
            'medical_certificate' => 'medical_certificate_' . fake()->date('Y_m_d') . '.pdf',
            'passport_photo' => 'passport_photo_' . fake()->uuid() . '.jpg',
            'recommendation_letter' => 'recommendation_letter_' . fake()->lastName() . '.pdf',
            'essay_document' => 'scholarship_essay_' . fake()->date('Y_m_d') . '.pdf',
            'certificate_of_indigency' => 'certificate_of_indigency_' . fake()->date('Y_m_d') . '.pdf',
            'family_income_certificate' => 'family_income_cert_' . fake()->date('Y_m_d') . '.pdf',
            'athletic_performance_record' => 'athletic_record_' . fake()->date('Y_m_d') . '.pdf',
            'coach_recommendation_letter' => 'coach_recommendation_' . fake()->date('Y_m_d') . '.pdf',
            'sports_participation_certificate' => 'sports_participation_' . fake()->date('Y_m_d') . '.pdf',
            'certificate_of_tribal_membership' => 'tribal_membership_' . fake()->date('Y_m_d') . '.pdf',
            'cultural_endorsement_letter' => 'cultural_endorsement_' . fake()->date('Y_m_d') . '.pdf',
            'community_recommendation_letter' => 'community_recommendation_' . fake()->date('Y_m_d') . '.pdf',
            'ancestral_domain_certificate' => 'ancestral_domain_cert_' . fake()->date('Y_m_d') . '.pdf',
            'valid_id' => 'valid_id_' . fake()->uuid() . '.jpg',
            'utility_bills' => 'utility_bills_' . fake()->date('Y_m_d') . '.pdf',
            'parent_employment_certificate' => 'employment_cert_' . fake()->date('Y_m_d') . '.pdf',
            'certificate_of_awards' => 'awards_certificate_' . fake()->date('Y_m_d') . '.pdf',
            'academic_recommendation_letter' => 'academic_recommendation_' . fake()->date('Y_m_d') . '.pdf',
            'medical_clearance_sports' => 'medical_clearance_sports_' . fake()->date('Y_m_d') . '.pdf',
            'team_membership_certificate' => 'team_membership_' . fake()->date('Y_m_d') . '.pdf',
            default => strtolower($documentType) . '_' . fake()->date('Y_m_d') . '.pdf'
        };
    }

    /**
     * Get MIME type based on document type
     */
    private function getMimeType(string $documentType): string
    {
        return match ($documentType) {
            'passport_photo', 'valid_id' => 'image/jpeg',
            default => 'application/pdf'
        };
    }

    /**
     * Get file extension from MIME type
     */
    private function getExtension(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            default => 'pdf'
        };
    }
}
