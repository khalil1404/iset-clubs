{% extends 'base.html.twig' %}
{% block title %}{{ event.nomEvenement }}{% endblock %}
{% block body %}
<div class="row">
    <div class="col-md-8">
        {% if event.image %}
            <img src="/uploads/events/{{ event.image }}"
                 class="img-fluid rounded mb-3" style="max-height:300px;width:100%;object-fit:cover">
        {% endif %}
        <h2>{{ event.nomEvenement }}</h2>
        <p class="text-muted">
            <i class="bi bi-calendar3"></i> {{ event.dateDebut|date('d/m/Y H:i') }}
            &nbsp;→&nbsp; {{ event.dateFin|date('d/m/Y H:i') }}
            &nbsp;|&nbsp;
            <i class="bi bi-geo-alt"></i> {{ event.lieu }}
            &nbsp;|&nbsp;
            <i class="bi bi-people"></i> {{ participants|length }} participants
        </p>
        <p>{{ event.description }}</p>

        {% if is_granted('ROLE_USER') %}
            {% if not isRegistered %}
                <form method="post" action="{{ path('app_event_register', {id: event.id}) }}">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-calendar-check"></i> S'inscrire
                    </button>
                </form>
            {% else %}
                <form method="post" action="{{ path('app_event_unregister', {id: event.id}) }}">
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-calendar-x"></i> Se désinscrire
                    </button>
                </form>
            {% endif %}
        {% endif %}

        {% set feedbacks = event.feedback %}
        {% if feedbacks|length > 0 %}
            <hr>
            <h5>⭐ Feedbacks ({{ feedbacks|length }})</h5>
            {% set total = 0 %}
            {% for f in feedbacks %}{% set total = total + f.rating %}{% endfor %}
            <p class="text-muted">Note moyenne : <strong>{{ (total / feedbacks|length)|round(1) }}/5</strong></p>
            {% for f in feedbacks %}
                <div class="card mb-2">
                    <div class="card-body py-2">
                        <strong>{{ f.user.fullName }}</strong>
                        <span class="text-warning ms-2">
                            {% for i in 1..f.rating %}⭐{% endfor %}
                        </span>
                        <p class="mb-0 small text-muted">{{ f.content }}</p>
                    </div>
                </div>
            {% endfor %}
        {% endif %}

        {% if isRegistered %}
            <hr>
            <h5>Laisser un feedback</h5>
            <form method="post" action="{{ path('app_feedback_new', {id: event.id}) }}">
                <div class="mb-2">
                    <label class="form-label">Note</label>
                    <select name="rating" class="form-select" required>
                        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                        <option value="4">⭐⭐⭐⭐ Bien</option>
                        <option value="3">⭐⭐⭐ Moyen</option>
                        <option value="2">⭐⭐ Mauvais</option>
                        <option value="1">⭐ Très mauvais</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Commentaire</label>
                    <textarea name="content" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    Envoyer le feedback
                </button>
            </form>
        {% endif %}

    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header"><strong>Club organisateur</strong></div>
            <div class="card-body text-center">
                <h5>{{ event.club.name }}</h5>
                <span class="badge bg-secondary">{{ event.club.domain }}</span>
            </div>
        </div>
    </div>
</div>
{% endblock %}