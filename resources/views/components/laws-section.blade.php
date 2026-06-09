@props(['laws' => collect()])

<style>
    .laws-section {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eef0f9;
        box-shadow: 0 1px 6px rgba(0, 0, 0, .05);
        overflow: hidden;
        margin-top: 2rem;
    }

    .laws-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f0f2fb;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .laws-header h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #1a1f36;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .laws-header i {
        color: #273475;
        font-size: 1.1rem;
    }

    .laws-content {
        padding: 1rem 0;
    }

    .law-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f9fafb;
        transition: background 0.2s;
        cursor: pointer;
    }

    .law-item:last-child {
        border-bottom: none;
    }

    .law-item:hover {
        background: #f8f9ff;
    }

    .law-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1a1f36;
        margin-bottom: 0.3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .law-category {
        display: inline-block;
        background: #e6f7ed;
        color: #00963F;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
    }

    .law-description {
        font-size: 0.85rem;
        color: #6b7280;
        line-height: 1.4;
        margin-bottom: 0.5rem;
    }

    .law-details {
        font-size: 0.8rem;
        color: #9ca3af;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .law-number {
        background: #eef0f9;
        color: #273475;
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-weight: 600;
    }

    .laws-empty {
        padding: 3rem 1.5rem;
        text-align: center;
        color: #9ca3af;
    }

    .laws-empty i {
        font-size: 2rem;
        opacity: 0.3;
        display: block;
        margin-bottom: 0.5rem;
    }

    .law-item-toggle {
        background: none;
        border: none;
        color: #273475;
        font-size: 1rem;
        cursor: pointer;
        padding: 0;
        transition: transform 0.2s;
    }

    .law-item-toggle.expanded {
        transform: rotate(180deg);
    }

    .law-articles {
        display: none;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #f0f2fb;
    }

    .law-articles.visible {
        display: block;
    }

    .law-article {
        font-size: 0.8rem;
        color: #374151;
        margin-bottom: 0.3rem;
        padding-left: 0.5rem;
        border-left: 2px solid #273475;
        padding-bottom: 0.3rem;
    }

    .law-article strong {
        color: #273475;
        font-weight: 600;
    }

    .law-notes {
        background: #f8f9ff;
        border-left: 3px solid #273475;
        padding: 0.6rem 0.8rem;
        border-radius: 4px;
        font-size: 0.8rem;
        color: #374151;
        margin-top: 0.5rem;
        line-height: 1.4;
    }
</style>

<div class="laws-section">
    <div class="laws-header">
        <h3>
            <i class="bi bi-file-earmark-text"></i>
            Leyes Colombianas Aplicables
        </h3>
        <span style="font-size: 0.8rem; color: #9ca3af;">{{ $laws->count() }} leyes</span>
    </div>

    <div class="laws-content">
        @if($laws->count() > 0)
            @foreach($laws as $law)
                <div class="law-item">
                    <div class="law-title">
                        <div>
                            <strong>{{ $law->title }}</strong>
                            <span class="law-category" style="margin-left: 0.75rem;">{{ $law->category }}</span>
                        </div>
                        @if($law->relevant_articles && count($law->relevant_articles) > 0)
                            <button type="button" class="law-item-toggle" onclick="toggleLawDetails(this, {{$loop->index }})">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        @endif
                    </div>

                    <div class="law-description">
                        {{ $law->description }}
                    </div>

                    <div class="law-details">
                        @if($law->law_number)
                            <span class="law-number">{{ $law->law_number }}</span>
                        @endif
                        @if($law->publication_date)
                            <span>📅 {{ $law->publication_date->format('d/m/Y') }}</span>
                        @endif
                    </div>

                    {{-- Artículos relevantes (expandible) --}}
                    @if($law->relevant_articles && count($law->relevant_articles) > 0)
                        <div class="law-articles" id="law-{{ $loop->index }}">
                            <strong style="color: #273475; font-size: 0.8rem;">Artículos Principales:</strong>
                            @foreach($law->relevant_articles as $article => $content)
                                <div class="law-article">
                                    <strong>{{ $article }}:</strong> {{ $content }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Notas de implementación --}}
                    @if($law->implementation_notes)
                        <div class="law-notes">
                            <strong>💡 Nota de Implementación:</strong> {{ $law->implementation_notes }}
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="laws-empty">
                <i class="bi bi-file-earmark-x"></i>
                <p>No hay leyes aplicables para tu perfil en este momento.</p>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleLawDetails(button, index) {
        const details = document.getElementById(`law-${index}`);
        if (details) {
            details.classList.toggle('visible');
            button.classList.toggle('expanded');
        }
    }
</script>