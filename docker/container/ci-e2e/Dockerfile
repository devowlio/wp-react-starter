FROM cypress/included:3.2.0

RUN apt-get update
RUN apt-get install -y \
    # Pyhton is needed for Docker-Compose
    python \
    python-pip \
    python-dev

# Docker and Docker-Compose (via Python, PiP)
RUN curl -sSL https://get.docker.com/ | sh
RUN pip install docker-compose

# Display versions installed
RUN docker -v
RUN docker-compose -v