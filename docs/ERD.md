# ENTITY RELATIONSHIP DIAGRAM (ERD) — SMA1LE LEARNING SYSTEM
**Arsitektur Basis Data Perencanaan Pembelajaran, LMS, Asesmen & Refleksi**  
*SMAN 1 Lengkong — Laravel 13*

---

## 1. Diagram Relasi Entitas Terpadu (Mermaid ERD)

```mermaid
erDiagram
    SUBJECTS ||--o{ LEARNING_OUTCOMES : "memiliki CP"
    SUBJECTS ||--o{ LEARNING_OBJECTIVES : "memiliki TP"
    SUBJECTS ||--o{ COURSE_CLASSES : "diampu pada"
    
    LEARNING_OUTCOMES ||--o{ LEARNING_OBJECTIVES : "diturunkan menjadi"
    
    TEACHERS ||--o{ COURSE_CLASSES : "mengampu"
    TEACHERS ||--o{ LEARNING_OBJECTIVES : "merumuskan"
    TEACHERS ||--o{ LESSON_PLANS : "menyusun"
    TEACHERS ||--o{ TEACHER_REFLECTIONS : "menulis refleksi"

    SCHOOL_CLASSES ||--o{ COURSE_CLASSES : "rombongan belajar"
    ACADEMIC_YEARS ||--o{ COURSE_CLASSES : "periode tahun ajaran"

    COURSE_CLASSES ||--o{ LESSON_PLANS : "memiliki modul ajar"
    COURSE_CLASSES ||--o{ LMS_MATERIALS : "membagikan materi"
    COURSE_CLASSES ||--o{ LMS_ASSIGNMENTS : "memberikan tugas"
    COURSE_CLASSES ||--o{ LMS_QUIZZES : "menyelenggarakan kuis"
    COURSE_CLASSES ||--o{ TEACHER_REFLECTIONS : "evaluasi KBM"
    COURSE_CLASSES ||--o{ REMEDIATION_PLANS : "tindak lanjut remedial"

    LESSON_PLANS ||--|{ LESSON_PLAN_TPS : "memuat target"
    LEARNING_OBJECTIVES ||--|{ LESSON_PLAN_TPS : "diajarkan dalam"

    LEARNING_OBJECTIVES ||--o{ LMS_MATERIALS : "dasar materi"
    LEARNING_OBJECTIVES ||--o{ LMS_ASSIGNMENTS : "dasar tugas"

    STUDENTS ||--o{ LMS_ASSIGNMENT_SUBMISSIONS : "mengumpulkan tugas"
    LMS_ASSIGNMENTS ||--o{ LMS_ASSIGNMENT_SUBMISSIONS : "menerima jawaban"

    STUDENTS ||--o{ STUDENT_PORTFOLIOS : "memiliki karya"
    LEARNING_OBJECTIVES ||--o{ STUDENT_PORTFOLIOS : "bukti capaian"

    USERS ||--o{ LESSON_PLANS : "direview oleh"
    USERS ||--o{ STUDENT_PORTFOLIOS : "diverifikasi oleh"
```

---

## 2. Alur Keterlacakan Pedagogis (Pedagogical Traceability Flow)

1. **Capaian Pembelajaran (CP)**: Standar kompetensi per fase (Fase E/F) dari Kementerian Pendidikan Dasar dan Menengah.
2. **Tujuan Pembelajaran (TP)**: Jabaran operasional kompetensi dan ruang lingkup materi yang dirumuskan guru per mata pelajaran.
3. **Alur Tujuan Pembelajaran (ATP)**: Rangkaian urutan penyampaian TP secara sistematis sepanjang semester.
4. **Modul Ajar / RPP Digital**: Perencanaan detail pertemuan KBM (Apersepsi, Kegiatan Inti, Penutup, LKPD, Rubrik) yang mengaitkan satu atau beberapa TP via tabel pivot `lesson_plan_tps`.
5. **Pelaksanaan LMS**: Materi (`lms_materials`) dan tugas (`lms_assignments`) menautkan `learning_objective_id` agar ketercapaian siswa dapat diukur secara presisi.
6. **Refleksi & Remedial**: Pasca KBM, guru mencatat refleksi (`teacher_reflections`) dan menyusun rencana tindak lanjut remedial (`remediation_enrichment_plans`) bagi siswa yang memerlukan bimbingan tambahan.

