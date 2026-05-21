#!/usr/bin/env python3
"""
edit_surat.py
Mengubah HANYA paragraf "Berkaitan..." dalam surat riset BSI
menjadi paragraf sosialisasi, sambil mempertahankan format asli PDF.
"""

import fitz  # PyMuPDF
import sys
import os

def edit_surat(input_pdf_path, output_pdf_path, judul, matkul):
    """
    Membuka PDF asli, mengganti paragraf target, menyimpan ke output.
    
    :param input_pdf_path: Path ke PDF surat asli dari BSI
    :param output_pdf_path: Path output PDF yang sudah diedit
    :param judul: Judul/tema materi sosialisasi
    :param matkul: Nama mata kuliah
    :return: True jika sukses, False jika gagal
    """
    try:
        doc = fitz.open(input_pdf_path)
        page = doc[0]

        # -------------------------------------------------------
        # Teks baru yang menggantikan paragraf lama
        # -------------------------------------------------------
        new_text = (
            f'Berkaitan dengan program pemerintah di bidang pendidikan dalam mewujudkan '
            f'keterkaitan dan kesepadanan (Link and Match) antara dunia pendidikan dan dunia '
            f'kerja, maka kami Universitas Bina Sarana Informatika mengajukan permohonan izin '
            f'untuk dapat melaksanakan kegiatan sosialisasi dan edukasi terkait program '
            f'"{judul}" untuk mata kuliah {matkul}. '
            f'Kegiatan ini ditujukan kepada siswa/i maupun staf pengajar di sekolah/instansi '
            f'yang Bapak/Ibu pimpin guna menyelaraskan perkembangan teknologi data di industri '
            f'dengan kurikulum pendidikan.'
        )

        # -------------------------------------------------------
        # Deteksi posisi paragraf lama secara dinamis
        # -------------------------------------------------------
        blocks = page.get_text("dict")["blocks"]
        para_y_start = None
        para_y_end   = None

        TRIGGER_KEYWORDS = [
            "Berkaitan dengan program pemerintah",
            "pimpin. Dimana lama pelaksanaan",
        ]

        for b in blocks:
            if b["type"] != 0:
                continue
            for line in b["lines"]:
                for span in line["spans"]:
                    txt = span["text"]
                    if "Berkaitan dengan program pemerintah" in txt:
                        para_y_start = span["bbox"][1] - 3
                    if "pimpin. Dimana lama pelaksanaan" in txt:
                        para_y_end = span["bbox"][3] + 3

        # Fallback ke koordinat hard-coded jika deteksi gagal
        if para_y_start is None:
            para_y_start = 274.0
        if para_y_end is None:
            para_y_end = 397.0

        x_left  = 73.0
        x_right = 522.0

        # -------------------------------------------------------
        # Hapus paragraf lama dengan kotak putih
        # -------------------------------------------------------
        whiteout_rect = fitz.Rect(x_left, para_y_start, x_right, para_y_end)
        page.draw_rect(whiteout_rect, color=(1, 1, 1), fill=(1, 1, 1))

        # -------------------------------------------------------
        # Sisipkan paragraf baru (justified, Helvetica 11pt)
        # -------------------------------------------------------
        html_new = (
            f'<p style="font-family: Helvetica; font-size: 11pt; '
            f'text-align: justify; margin: 0; padding: 0; line-height: 14pt;">'
            f'{new_text}</p>'
        )

        text_rect = fitz.Rect(73.7, para_y_start + 3, 521.6, para_y_end + 30)
        spare, scale = page.insert_htmlbox(text_rect, html_new)

        if spare < 0:
            # Jika tidak muat, coba dengan ukuran lebih kecil sedikit
            html_new_small = (
                f'<p style="font-family: Helvetica; font-size: 10.5pt; '
                f'text-align: justify; margin: 0; padding: 0; line-height: 13.5pt;">'
                f'{new_text}</p>'
            )
            page.draw_rect(whiteout_rect, color=(1, 1, 1), fill=(1, 1, 1))
            spare, scale = page.insert_htmlbox(text_rect, html_new_small)

        doc.save(output_pdf_path, garbage=4, deflate=True)
        doc.close()
        return True

    except Exception as e:
        print(f"ERROR: {e}", file=sys.stderr)
        return False


if __name__ == "__main__":
    # CLI usage: python3 edit_surat.py input.pdf output.pdf "Judul Materi" "Mata Kuliah"
    if len(sys.argv) < 5:
        print("Usage: python3 edit_surat.py <input.pdf> <output.pdf> <judul> <matkul>")
        sys.exit(1)

    ok = edit_surat(sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4])
    sys.exit(0 if ok else 1)
