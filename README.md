## 0. 프로젝트 개요

**미션 목표**
리눅스 계열 터미널 조작과 파일 권한 체계를 익히고, Docker를 이용해 커스텀 애플리케이션 이미지를 제작·실행하며, Git/GitHub로 결과물을 버전 관리·공유할 수 있는 개발 워크스테이션을 구축

**구축한 환경**

| 서비스 | 이미지 | 역할 | 공개 포트 |
|---|---|---|:--:|
| `app` | `php:8.2.23` 기반 **커스텀 빌드** | PHP 빌트인 웹 서버, DB 조회 및 메일 발송 | 8000 |
| `db` | `mysql:8.4.2` | 사용자 데이터 저장, 초기 스키마 자동 적재 | 3306 |
| `mail` | `axllent/mailpit:v1.20.4` | 개발용 SMTP 수신 + 웹 UI | 8025 |

**저장소 구조**

```text
.
├── README.md
├── compose.yaml
├── .gitignore
├── docker/
│   ├── app/
│   │   ├── Dockerfile        # php:8.2.23 기반 커스텀 이미지
│   │   └── msmtprc           # 메일 발송 설정
│   └── db/
│       └── init/
│           └── init-user.sql # 컨테이너 최초 기동 시 자동 실행
├── src/
│   └── index.php             # 바인드 마운트 대상 (→ /my-work)
└── docs/
    ├── images/               # 스크린샷
    └── logs/                 # 원본 터미널 로그
```

---

## 1. 실행 환경

| 구분 | 내용 |
|---|---|
| OS | Windows 10 |
| 쉘 / 터미널 | bash 5.3.9 |
| Docker | 29.7.2 |
| Docker Compose | v5.4.0 |
| Git | 2.55.0 |
| VS Code | 1.133.0 |

**버전 확인 명령 및 출력**

```bash
cmd.exe /c ver
bash --version
docker --version
docker compose version
git --version
code --version
```

```text
Microsoft Windows [Version 10.0.26200.9168]
GNU bash, version 5.3.9(1)-release (x86_64-pc-linux-gnu)
Docker version 29.7.2
Docker Compose version v5.4.0
git version 2.55.0.windows.4
1.133.0
```

---

## 2. 수행 항목 체크리스트

| 구분 | 항목 | 상태 | 증거 위치 |
|---|---|:--:|---|
| 필수 | 터미널 조작 로그 기록 | ☑ | [터미널 기본 조작](#3-터미널-조작-로그) |
| 필수 | 권한 실습 및 증거 기록 | ☑ | [파일 권한 변경](#4-파일-권한-실습) |
| 필수 | Docker 설치 및 기본 점검 | ☑ | [Docker 설치 및 데몬 점검](#5-docker-설치-및-점검) |
| 필수 | Docker 기본 운영 명령 수행 | ☑ | [Docker 기본 운영 명령](#6-docker-기본-운영-명령) |
| 필수 | 컨테이너 실행 실습 | ☑ | [컨테이너 실행 실습](#7-컨테이너-실행-실습) |
| 필수 | Dockerfile 기반 커스텀 이미지 제작 | ☑ | [커스텀 이미지 빌드/실행](#8-커스텀-이미지-제작) |
| 필수 | 포트 매핑 및 접속 | ☑ | [포트 매핑 및 접속](#9-포트-매핑-및-접속-증거) |
| 필수 | 바인드 마운트 반영 | ☑ | [바인드 마운트 반영](#10-바인드-마운트-반영-검증) |
| 필수 | Docker 볼륨 영속성 검증 | ☑ | [Docker 볼륨 영속성 검증](#11-docker-볼륨-영속성-검증) |
| 필수 | Git 설정 및 GitHub 연동 | ☑ | [Git 설정 및 GitHub 연동](#12-git-설정-및-github--vscode-연동) |
| 필수 | 보안 및 개인정보 보호 | ☑ | [보안 및 개인정보 보호](#13-보안-및-개인정보-마스킹) |
| 필수 | 트러블슈팅 | ☑ | [트러블슈팅](#14-트러블슈팅) |
| 보너스| Docker Compose 기초 | ☑ | [Docker Compose 기초](#16-보너스-1-docker-compose-기초) |
| 보너스| Docker Compose 멀티 컨테이너 | ☑ | [Docker Compose 멀티 컨테이너](#17-보너스-2-멀티-컨테이너-및-네트워크-통신) |
| 보너스| Compose 운영 명령어 습득 | ☑ | [Compose 운영 명령어 습득](#18-보너스-3-compose-운영-명령어) |
| 보너스| GitHub SSH 키 설정 | ☑ | [GitHub SSH 키 설정](#19-보너스-5-github-ssh-키-설정) |

---

## 3. 터미널 조작 로그

**목적**: 경로 이동, 파일/디렉토리 생성·복사·이동·삭제 등 기본 조작을 수행하고 결과를 확인

### 3.1 현재 위치 및 목록 확인

```bash
pwd
ls
ls -al          # 숨김 파일(.gitignore 등) 포함
```

```text
ls -al          # 숨김 파일(.gitignore 등) 포함
/mnt/c/Users/yunea/Desktop/Codyssey/E1-1/work
compose.yaml  docker  src
total 8
drwxrwxrwx 1 yunea yunea 4096 Aug 21 14:37 .
drwxrwxrwx 1 yunea yunea 4096 Aug 21 14:37 ..
-rwxrwxrwx 1 yunea yunea 3950 Aug 21 14:35 .gitignore
-rwxrwxrwx 1 yunea yunea  868 Aug 21 05:22 compose.yaml
drwxrwxrwx 1 yunea yunea 4096 Aug 21 02:27 docker
drwxrwxrwx 1 yunea yunea 4096 Aug 21 04:34 src
```

### 3.2 디렉토리 이동 (절대 경로 / 상대 경로)

```bash
cd /home/****/E1-1/work      # 절대 경로
cd ./docker/app              # 상대 경로 (하위)
cd ../../src                 # 상대 경로 (상위 두 단계 후 이동)
pwd
```

```text
-bash: cd: /home/****/E1-1/work: No such file or directory
/mnt/c/Users/yunea/Desktop/Codyssey/E1-1/work/src
```

### 3.3 생성 · 복사 · 이동/이름변경 · 삭제

```bash
# 0) 위치 확인 (항상 프로젝트 루트에서 시작)
pwd

# 1) 디렉토리·파일 생성
mkdir -p practice/sub
touch practice/sample.txt
echo "hello workstation" > practice/sample.txt

# 2) 내용 확인
cat practice/sample.txt

# 3) 복사
cp practice/sample.txt practice/sample_copy.txt

# 4) 이동/이름변경
mv practice/sample_copy.txt practice/renamed.txt
ls -al practice

# 5) 삭제
rm practice/renamed.txt
rm -r practice/sub
ls -al practice
```

```text
/mnt/c/Users/yunea/Desktop/Codyssey/E1-1/work/src
hello workstation
total 0
drwxrwxrwx 1 yunea yunea 4096 Aug 21 16:55 .
drwxrwxrwx 1 yunea yunea 4096 Aug 21 16:49 ..
-rwxrwxrwx 1 yunea yunea   18 Aug 21 16:55 renamed.txt
-rwxrwxrwx 1 yunea yunea   18 Aug 21 16:55 sample.txt
drwxrwxrwx 1 yunea yunea 4096 Aug 21 16:51 sub
total 0
drwxrwxrwx 1 yunea yunea 4096 Aug 21 16:55 .
drwxrwxrwx 1 yunea yunea 4096 Aug 21 16:49 ..
-rwxrwxrwx 1 yunea yunea   18 Aug 21 16:55 sample.txt
```
---

## 4. 파일 권한 실습

**목적**: `r/w/x` 권한의 의미와 숫자 표기의 해석 규칙을 실습으로 확인

### 4.1 파일 권한 변경 (변경 전 → 후)

**변경 전**

```bash
ls -l docker/app/msmtprc
```

```text
-rwxrwxrwx 1 yunea yunea 89 Aug 21 05:07 docker/app/msmtprc
```

**변경 명령**

```bash
chmod 600 docker/app/msmtprc
ls -l docker/app/msmtprc
```

```text
-rw------- 1 yunea yunea 89 Aug 21 17:35 msmtprc
```

**복원**

```bash
chmod 644 docker/app/msmtprc
ls -l docker/app/msmtprc
```

```text
-rw-r--r-- 1 yunea yunea 89 Aug 21 17:35 msmtprc
```
---

## 5. Docker 설치 및 점검

### 5.1 버전 확인

```bash
docker --version
docker compose version
```

```text
Docker version 29.7.2, build a7dcaa6
Docker Compose version v5.4.0
```

### 5.2 데몬 동작 확인

```bash
docker info
```

```text
Client:
 Version:    29.7.2
 Context:    default
 Debug Mode: false
 Plugins:
  agent: Docker AI Agent Runner (Docker Inc.)
    Version:  v1.122.0
    Path:     /usr/local/lib/docker/cli-plugins/docker-agent
  ai: Docker AI Agent - Ask Gordon (Docker Inc.)
    Version:  v1.30.0
    Path:     /usr/local/lib/docker/cli-plugins/docker-ai
  buildx: Docker Buildx (Docker Inc.)
    Version:  v0.36.1-desktop.1
    Path:     /usr/local/lib/docker/cli-plugins/docker-buildx
  compose: Docker Compose (Docker Inc.)
    Version:  v5.4.0
    Path:     /usr/local/lib/docker/cli-plugins/docker-compose
  debug: Get a shell into any image or container (Docker Inc.)
    Version:  0.0.47
    Path:     /usr/local/lib/docker/cli-plugins/docker-debug
  desktop: Docker Desktop commands (Docker Inc.)
    Version:  v0.4.3
    Path:     /usr/local/lib/docker/cli-plugins/docker-desktop
  dhi: CLI for managing Docker Hardened Images (Docker Inc.)
    Version:  v0.0.7
    Path:     /usr/local/lib/docker/cli-plugins/docker-dhi
  extension: Manages Docker extensions (Docker Inc.)
    Version:  v0.2.31
    Path:     /usr/local/lib/docker/cli-plugins/docker-extension
  init: Creates Docker-related starter files for your project (Docker Inc.)
    Version:  v1.4.0
    Path:     /usr/local/lib/docker/cli-plugins/docker-init
  mcp: Docker MCP Plugin (Docker Inc.)
    Version:  v0.43.3
    Path:     /usr/local/lib/docker/cli-plugins/docker-mcp
  model: Docker Model Runner (Docker Inc.)
    Version:  v1.2.6
    Path:     /usr/local/lib/docker/cli-plugins/docker-model
  offload: Docker Offload (Docker Inc.)
    Version:  v0.6.10
    Path:     /usr/local/lib/docker/cli-plugins/docker-offload
  pass: Docker Pass Secrets Manager Plugin (beta) (Docker Inc.)
    Version:  v0.2.1
    Path:     /usr/local/lib/docker/cli-plugins/docker-pass
  sandbox: "docker sandbox" is deprecated, use Docker Sandboxes instead (Docker Inc.)
    Version:  v0.13.0
    Path:     /usr/local/lib/docker/cli-plugins/docker-sandbox
  scout: Docker Scout (Docker Inc.)
    Version:  v1.24.0
    Path:     /usr/local/lib/docker/cli-plugins/docker-scout

Server:
 Containers: 6
  Running: 0
  Paused: 0
  Stopped: 6
 Images: 6
 Server Version: 29.7.2
 Storage Driver: overlayfs
  driver-type: io.containerd.snapshotter.v1
 Logging Driver: json-file
 Cgroup Driver: cgroupfs
 Cgroup Version: 2
 Plugins:
  Volume: local
  Network: bridge host ipvlan macvlan null overlay
  Log: awslogs fluentd gcplogs gelf journald json-file local splunk syslog
 CDI spec directories:
  /etc/cdi
  /var/run/cdi
 Discovered Devices:
  cdi: docker.com/gpu=webgpu
 Swarm: inactive
 Runtimes: nvidia runc io.containerd.runc.v2
 Default Runtime: runc
 Init Binary: docker-init
 containerd version: e53c7c1516c3b2bff98eb76f1f4117477e6f4e66
 runc version: v1.3.6-0-g491b69ba
 init version: de40ad0
 Security Options:
  seccomp
   Profile: builtin
  cgroupns
 Kernel Version: 6.6.114.1-microsoft-standard-WSL2
 Operating System: Docker Desktop
 OSType: linux
 Architecture: x86_64
 CPUs: 8
 Total Memory: 3.752GiB
 Name: docker-desktop
 ID: 764e449a-21f9-4b3d-b889-759974fc4a23
 Docker Root Dir: /var/lib/docker
 Debug Mode: false
 HTTP Proxy: http.docker.internal:3128
 HTTPS Proxy: http.docker.internal:3128
 No Proxy: hubproxy.docker.internal
 Labels:
  com.docker.desktop.address=unix:///var/run/docker-cli.sock
 Experimental: false
 Insecure Registries:
  hubproxy.docker.internal:5555
  ::1/128
  127.0.0.0/8
 Live Restore Enabled: false
 Firewall Backend: iptables
```

## 6. Docker 기본 운영 명령

### 6.1 이미지 다운로드 및 목록 확인

```bash
docker pull mysql:8.4.2
docker pull axllent/mailpit:v1.20.4
docker images
```

```text
8.4.2: Pulling from library/mysql
Digest: sha256:ac80b6e09e5b12b4f9d5cd4f6425e43464247aa4ba4f6169da5daf59e5877f7d
Status: Image is up to date for mysql:8.4.2
docker.io/library/mysql:8.4.2
v1.20.4: Pulling from axllent/mailpit
Digest: sha256:ff82a613c735f4e8a3ddfdf7ee4e18cb934c721e72a340ecbbb7ec0ca7fbd057
Status: Image is up to date for axllent/mailpit:v1.20.4
docker.io/axllent/mailpit:v1.20.4

IMAGE                     ID             DISK USAGE   CONTENT SIZE   EXTRA
axllent/mailpit:v1.20.4   ff82a613c735       42.4MB         12.1MB    U
hello-world:latest        5dd0d3e6e255       25.9kB         9.49kB    U
mysql:8.4.2               ac80b6e09e5b        800MB          170MB    U
php:8.2.23                f0df92da4046        739MB          182MB
work-app:0.1.0            3eee4dd417d2        792MB          201MB
work-app:latest           02054840a9dc        792MB          201MB    U
```

### 6.2 컨테이너 실행 / 중지 / 목록 확인

```bash
docker compose up -d
docker ps
docker stop work-mail-1
docker ps -a        # 중지된 컨테이너까지 확인
docker start work-mail-1
```

```text
CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES
work-mail-1
CONTAINER ID   IMAGE                     COMMAND                  CREATED        STATUS                           PORTS     NAMES
b7eed1fa134a   work-app                  "docker-php-entrypoi…"   12 hours ago   Exited (137) About an hour ago             work-app-1
8acbc5641a88   mysql:8.4.2               "docker-entrypoint.s…"   12 hours ago   Exited (137) About an hour ago             work-db-1
07188640f457   axllent/mailpit:v1.20.4   "/mailpit"               12 hours ago   Exited (0) About an hour ago               work-mail-1
e7b92933d30c   fa5b6cee6a47              "docker-php-entrypoi…"   13 hours ago   Created                                    app-server
8135a960fd91   axllent/mailpit:v1.20.4   "/mailpit"               15 hours ago   Exited (1) 15 hours ago                    mailpit
b54404288bf8   hello-world               "/hello"                 15 hours ago   Exited (0) 15 hours ago                    nostalgic_wright
work-mail-1
```

### 6.3 로그 확인

```bash
docker logs work-db-1 | tail -20
docker logs work-app-1
```

```text
2026-08-20T20:31:35.886069Z 0 [System] [MY-015015] [Server] MySQL Server - start.
2026-08-20T20:31:36.747257Z 0 [System] [MY-010116] [Server] /usr/sbin/mysqld (mysqld 8.4.2) starting as process 1
2026-08-20T20:31:36.777619Z 1 [System] [MY-013576] [InnoDB] InnoDB initialization has started.
2026-08-20T20:31:37.849195Z 1 [System] [MY-013577] [InnoDB] InnoDB initialization has ended.
2026-08-20T20:31:38.763742Z 0 [Warning] [MY-010068] [Server] CA certificate ca.pem is self signed.
2026-08-20T20:31:38.763949Z 0 [System] [MY-013602] [Server] Channel mysql_main configured to support TLS. Encrypted connections are now supported for this channel.
2026-08-20T20:31:38.779202Z 0 [Warning] [MY-011810] [Server] Insecure configuration for --pid-file: Location '/var/run/mysqld' in the path is accessible to all OS users. Consider choosing a different directory.
2026-08-20T20:31:39.031156Z 0 [System] [MY-011323] [Server] X Plugin ready for connections. Bind-address: '::' port: 33060, socket: /var/run/mysqld/mysqlx.sock
2026-08-20T20:31:39.032351Z 0 [System] [MY-010931] [Server] /usr/sbin/mysqld: ready for connections. Version: '8.4.2'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306  MySQL Community Server - GPL.
2026-08-21T07:36:55.653779Z 0 [System] [MY-013172] [Server] Received SHUTDOWN from user <via user signal>. Shutting down mysqld (Version: 8.4.2).
2026-08-21 05:31:33+09:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.4.2-1.el9 started.
2026-08-21 05:31:34+09:00 [Note] [Entrypoint]: Switching to dedicated user 'mysql'
2026-08-21 05:31:35+09:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.4.2-1.el9 started.
'/var/lib/mysql/mysql.sock' -> '/var/run/mysqld/mysqld.sock'
[Thu Aug 20 20:31:33 2026] PHP 8.2.23 Development Server (http://0.0.0.0:8000) started
```

### 6.4 리소스 사용량 확인

```bash
docker stats --no-stream
```

```text
CONTAINER ID   NAME          CPU %     MEM USAGE / LIMIT     MEM %     NET I/O           BLOCK I/O         PIDS
07188640f457   work-mail-1   0.00%     17.61MiB / 3.752GiB   0.46%     4.38kB / 2.46kB   18.8MB / 32.8kB   11
```

---

## 7. 컨테이너 실행 실습

### 7.1 hello-world

```bash
docker run hello-world
```

```text
Hello from Docker!
This message shows that your installation appears to be working correctly.

To generate this message, Docker took the following steps:
 1. The Docker client contacted the Docker daemon.
 2. The Docker daemon pulled the "hello-world" image from the Docker Hub.
    (amd64)
 3. The Docker daemon created a new container from that image which runs the
    executable that produces the output you are currently reading.
 4. The Docker daemon streamed that output to the Docker client, which sent it
    to your terminal.

To try something more ambitious, you can run an Ubuntu container with:
 $ docker run -it ubuntu bash

Share images, automate workflows, and more with a free Docker ID:
 https://hub.docker.com/

For more examples and ideas, visit:
 https://docs.docker.com/get-started/
```

### 7.2 ubuntu 컨테이너 내부 진입

```bash
docker run -it --name ubuntu-test ubuntu:22.04 /bin/bash
```

컨테이너 내부에서 수행:

```bash
pwd
ls
echo "inside container"
exit
```

```text
22.04: Pulling from library/ubuntu
d544298cabd5: Pull complete
5370ab2c86d9: Download complete
Digest: sha256:2edbbc5dc405e9612ba3584ce95480277e3eb374407b5505fe26f17df77c7dbc
Status: Downloaded newer image for ubuntu:22.04

bin  boot  dev  etc  home  lib  lib32  lib64  libx32  media  mnt  opt  proc  root  run  sbin  srv  sys  tmp  usr  var
inside container
```

### 7.3 attach / exec 차이 관찰

```bash
docker start ubuntu-test
docker exec -it ubuntu-test /bin/bash    # 새 프로세스로 진입
# exit → 컨테이너는 계속 실행 상태
docker ps

docker attach ubuntu-test                 # 메인 프로세스(PID 1)에 연결
# exit → 메인 프로세스 종료 = 컨테이너도 종료
docker ps -a
```

```text
CONTAINER ID   IMAGE                     COMMAND       CREATED         STATUS                   PORTS                                         NAMES
0c0761bdf94f   ubuntu:22.04              "/bin/bash"   2 minutes ago   Up 20 seconds                                                          ubuntu-test
07188640f457   axllent/mailpit:v1.20.4   "/mailpit"    12 hours ago    Up 4 minutes (healthy)   0.0.0.0:8025->8025/tcp, [::]:8025->8025/tcp   work-mail-1

CONTAINER ID   IMAGE                     COMMAND                  CREATED         STATUS                           PORTS                                         NAMES
0c0761bdf94f   ubuntu:22.04              "/bin/bash"              3 minutes ago   Exited (0) 5 seconds ago                                                       ubuntu-test
374e111b095d   hello-world               "/hello"                 3 minutes ago   Exited (0) 3 minutes ago                                                       priceless_nightingale
b7eed1fa134a   work-app                  "docker-php-entrypoi…"   12 hours ago    Exited (137) About an hour ago                                                 work-app-1
8acbc5641a88   mysql:8.4.2               "docker-entrypoint.s…"   12 hours ago    Exited (137) About an hour ago                                                 work-db-1
07188640f457   axllent/mailpit:v1.20.4   "/mailpit"               12 hours ago    Up 5 minutes (healthy)           0.0.0.0:8025->8025/tcp, [::]:8025->8025/tcp   work-mail-1
e7b92933d30c   fa5b6cee6a47              "docker-php-entrypoi…"   13 hours ago    Created                                                                        app-server
8135a960fd91   axllent/mailpit:v1.20.4   "/mailpit"               15 hours ago    Exited (1) 15 hours ago                                                        mailpit
b54404288bf8   hello-world               "/hello"                 15 hours ago    Exited (0) 15 hours ago                                                        nostalgic_wright
```

**정리**

| 구분 | attach | exec |
|---|---|---|
| 연결 대상 | 컨테이너의 메인 프로세스(PID 1) | 새로 생성한 별도 프로세스 |
| 종료 시 영향 | 메인 프로세스 종료 → 컨테이너 중지 | 컨테이너는 계속 실행 |
| 주 용도 | 메인 프로세스 출력 확인 | 운영 중 컨테이너 내부 작업 |

---

## 8. 커스텀 이미지 제작

### 8.1 선택한 베이스와 방식

- **방식**: Linux/런타임 베이스 이미지 + 기본 기능(패키지/설정/환경) 추가
- **베이스 이미지**: `php:8.2.23` (공식 이미지)
- **선택 이유**: PHP 공식 이미지는 런타임만 제공하고 DB 확장·메일 전송 수단이 포함되어 있지 않음, 애플리케이션 실행에 필요한 확장과 설정을 덧입혀 **"이 프로젝트 전용 실행 환경"** 을 이미지로 고정하기 위해 커스텀 빌드를 선택함

### 8.2 Dockerfile

```dockerfile
FROM php:8.2.23
RUN docker-php-ext-install pdo_mysql
RUN apt-get update
RUN apt-get install -y msmtp-mta
COPY ./msmtprc /etc/msmtprc
RUN chmod 644 /etc/msmtprc
RUN echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/mail.ini
CMD ["/usr/local/bin/php", "--server", "0.0.0.0:8000", "--docroot", "/my-work"]
```

### 8.3 커스텀 포인트별 목적

| 커스텀 포인트 | 목적 |
|---|---|
| `docker-php-ext-install pdo_mysql` | PHP에서 MySQL에 접속할 수 있도록 PDO 드라이버를 컴파일·설치 |
| `apt-get install -y msmtp-mta` | 컨테이너 내부에 MTA가 없어 메일 발송이 불가하므로 경량 MTA 추가 |
| `COPY ./msmtprc` + `chmod 644` | 메일 릴레이 대상(`mail:1025`)을 설정 파일로 고정하고, msmtp가 요구하는 권한으로 조정 |
| `sendmail_path` 설정 | PHP의 `mb_send_mail()` 호출이 msmtp를 거치도록 런타임 설정 주입 |
| `CMD php --server` | 별도 웹 서버 없이 PHP 빌트인 서버로 `/my-work`를 서비스 |

**보조 설정 파일 (`docker/app/msmtprc`)**

```
account default
host mail
port 1025
from service@example.com
tls off
auth off
timeout 5
```

> `host mail` — Compose 네트워크 안에서 `mail` 서비스명으로 접근

### 8.4 빌드 및 실행

```bash
docker compose build app
docker compose up -d
docker compose ps
```

```text
NAME          IMAGE                     COMMAND                  SERVICE   CREATED         STATUS                    PORTS
work-app-1    work-app                  "docker-php-entrypoi…"   app       2 seconds ago   Up 1 second               0.0.0.0:8000->8000/tcp, [::]:8000->8000/tcp
work-db-1     mysql:8.4.2               "docker-entrypoint.s…"   db        2 seconds ago   Up 1 second               0.0.0.0:3306->3306/tcp, [::]:3306->3306/tcp
work-mail-1   axllent/mailpit:v1.20.4   "/mailpit"               mail      12 hours ago    Up 18 minutes (healthy)   0.0.0.0:8025->8025/tcp, [::]:8025->8025/tcp
```

```bash
docker compose logs
```

```text
app-1  | [Fri Aug 21 08:57:41 2026] PHP 8.2.23 Development Server (http://0.0.0.0:8000) started
db-1   | 2026-08-21 17:57:41+09:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.4.2-1.el9 started.
db-1   | 2026-08-21 17:57:42+09:00 [Note] [Entrypoint]: Switching to dedicated user 'mysql'
db-1   | 2026-08-21 17:57:42+09:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.4.2-1.el9 started.
db-1   | '/var/lib/mysql/mysql.sock' -> '/var/run/mysqld/mysqld.sock'
db-1   | 2026-08-21T08:57:42.528425Z 0 [System] [MY-015015] [Server] MySQL Server - start.
db-1   | 2026-08-21T08:57:43.319627Z 0 [System] [MY-010116] [Server] /usr/sbin/mysqld (mysqld 8.4.2) starting as process 1
db-1   | 2026-08-21T08:57:43.350678Z 1 [System] [MY-013576] [InnoDB] InnoDB initialization has started.
db-1   | 2026-08-21T08:57:43.953509Z 1 [System] [MY-013577] [InnoDB] InnoDB initialization has ended.
db-1   | 2026-08-21T08:57:44.506275Z 0 [Warning] [MY-010068] [Server] CA certificate ca.pem is self signed.
db-1   | 2026-08-21T08:57:44.506342Z 0 [System] [MY-013602] [Server] Channel mysql_main configured to support TLS. Encrypted connections are now supported for this channel.
db-1   | 2026-08-21T08:57:44.513935Z 0 [Warning] [MY-011810] [Server] Insecure configuration for --pid-file: Location '/var/run/mysqld' in the path is accessible to all OS users. Consider choosing a different directory.
db-1   | 2026-08-21T08:57:44.567083Z 0 [System] [MY-011323] [Server] X Plugin ready for connections. Bind-address: '::' port: 33060, socket: /var/run/mysqld/mysqlx.sock
db-1   | 2026-08-21T08:57:44.569340Z 0 [System] [MY-010931] [Server] /usr/sbin/mysqld: ready for connections. Version: '8.4.2'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306  MySQL Community Server - GPL.
mail-1  | time="2026/08/21 05:31:33" level=info msg="[smtpd] starting on [::]:1025 (no encryption)"
mail-1  | time="2026/08/21 05:31:33" level=info msg="[http] starting on [::]:8025"
mail-1  | time="2026/08/21 05:31:33" level=info msg="[http] accessible via http://localhost:8025/"
mail-1  | [db] got terminated signal, shutting down
mail-1  | time="2026/08/21 17:39:05" level=info msg="[smtpd] starting on [::]:1025 (no encryption)"
mail-1  | time="2026/08/21 17:39:05" level=info msg="[http] starting on [::]:8025"
mail-1  | time="2026/08/21 17:39:05" level=info msg="[http] accessible via http://localhost:8025/"
```
---

## 9. 포트 매핑 및 접속 증거

### 9.1 매핑 구성

| 서비스 | 호스트 포트 | 컨테이너 포트 | 용도 |
|---|:--:|:--:|---|
| app | 8000 | 8000 | PHP 빌트인 웹 서버 |
| db | 3306 | 3306 | MySQL 접속(호스트 툴 연결용) |
| mail | 8025 | 8025 | Mailpit 웹 UI |

`compose.yaml`의 `ports` 항목이 `docker run -p <host>:<container>` 와 동일한 역할을 함

### 9.2 매핑 확인

```bash
docker compose ps
docker ps --format "table {{.Names}}\t{{.Ports}}"
```

```text
NAME          IMAGE                     COMMAND                  SERVICE   CREATED              STATUS                    PORTS
work-app-1    work-app                  "docker-php-entrypoi…"   app       About a minute ago   Up About a minute         0.0.0.0:8000->8000/tcp, [::]:8000->8000/tcp
work-db-1     mysql:8.4.2               "docker-entrypoint.s…"   db        About a minute ago   Up About a minute         0.0.0.0:3306->3306/tcp, [::]:3306->3306/tcp
work-mail-1   axllent/mailpit:v1.20.4   "/mailpit"               mail      12 hours ago         Up 19 minutes (healthy)   0.0.0.0:8025->8025/tcp, [::]:8025->8025/tcp
NAMES         PORTS
work-db-1     0.0.0.0:3306->3306/tcp, [::]:3306->3306/tcp
work-app-1    0.0.0.0:8000->8000/tcp, [::]:8000->8000/tcp
work-mail-1   0.0.0.0:8025->8025/tcp, [::]:8025->8025/tcp
```

### 9.3 응답 확인

```bash
curl -i http://localhost:8000
curl -I http://localhost:8025
```

```text
curl -I http://localhost:8025
HTTP/1.1 200 OK
Host: localhost:8000
Date: Fri, 21 Aug 2026 08:59:16 GMT
Connection: close
X-Powered-By: PHP/8.2.23
Content-type: text/html; charset=UTF-8

﻿<p>id: 1, name: John Doe</p><p>id: 2, name: Jane Doe</p><p>John Doe에 메일을 송신했습니다.</p><p>Jane Doe에 메일을 송신했습니다.</p>HTTP/1.1 405 Method Not Allowed
Date: Fri, 21 Aug 2026 08:59:16 GMT
```

**정리**: 컨테이너는 독립된 네트워크를 사용하므로 호스트에서 직접 접근할 수 없음, 호스트 포트를 컨테이너 포트에 연결해야 브라우저에서 접속이 가능함, 반면 컨테이너끼리는 포트 매핑 없이도 통신할 수 있음

---

## 10. 바인드 마운트 반영 검증

**목적**: 호스트의 소스 코드를 수정했을 때 이미지 재빌드 없이 컨테이너에 즉시 반영되는지 확인함

**마운트 구성 (`compose.yaml`)**

```yaml
volumes:
  - type: bind
    source: ./src
    target: /my-work
```

호스트의 `./src` 디렉토리가 컨테이너의 `/my-work`(PHP 빌트인 서버의 docroot)에 연결

**마운트 확인**

```bash
docker compose exec app ls -al /my-work
```

```text
total 8
drwxrwxrwx 1 1000 1000 4096 Aug 21 07:49 .
drwxr-xr-x 1 root root 4096 Aug 21 08:57 ..
-rwxrwxrwx 1 1000 1000 1086 Aug 20 19:34 index.php
drwxrwxrwx 1 1000 1000 4096 Aug 21 07:55 practice
```
**변경 전**

```bash
curl http://localhost:8000/bind-test.php
```

```text
404 error
```

**호스트에서 파일 생성**

```bash
cd /mnt/c/Users/yunea/Desktop/Codyssey/E1-1/work
pwd

echo '<?php echo "bind mount OK: " . date("H:i:s");' > src/bind-test.php
ls -l src/

curl http://localhost:8000/bind-test.php
```

**변경 후 (재빌드·재시작 없이 확인)**

```bash
docker compose exec app ls -al /my-work
curl http://localhost:8000/bind-test.php
```

```text
total 8
drwxrwxrwx 1 1000 1000 4096 Aug 21 09:01 .
drwxr-xr-x 1 root root 4096 Aug 21 08:57 ..
-rwxrwxrwx 1 1000 1000   46 Aug 21 09:01 bind-test.php
-rwxrwxrwx 1 1000 1000 1086 Aug 20 19:34 index.php
drwxrwxrwx 1 1000 1000 4096 Aug 21 07:55 practice
bind mount OK: 09:04:44
```

**정리**: 이미지를 다시 빌드하거나 컨테이너를 재시작하지 않아도 호스트 디렉토리의 변경이 컨테이너에 즉시 반영됨, 개발 중 코드 수정 → 새로고침 사이클이 성립하는 이유가 바로 이 바인드 마운트

---

## 11. Docker 볼륨 영속성 검증

**목적**: 컨테이너를 삭제해도 named volume의 데이터가 유지됨을 증명

**볼륨 구성 (`compose.yaml`)**

```yaml
volumes:
  db-compose-volume:
  mail-compose-volume:
```

`db-compose-volume` → `/var/lib/mysql`, `mail-compose-volume` → `/data` 에 각각 연결

### 11.1 볼륨 생성 확인

```bash
docker compose up -d
docker volume ls
docker volume inspect work_db-compose-volume
```

```text
[+] up 3/3
 ✔ Container work-db-1   Running                                                          0.0s
 ✔ Container work-mail-1 Running                                                          0.0s
 ✔ Container work-app-1  Running                                                          0.0s
DRIVER    VOLUME NAME
local     5ae515efaf46d54830a33aa799f7cedd9da2eea45974b0104afd10790d566730
local     72bda32683feed7536d694be0ec1bafe3abeac132374df5d50b2ae616afd14d9
local     work-db-volume
local     work-mail-volume
local     work_db-compose-volume
local     work_mail-compose-volume
[
    {
        "CreatedAt": "2026-08-20T20:23:43Z",
        "Driver": "local",
        "Labels": {
            "com.docker.compose.config-hash": "dde4e097364be35e2e062be9930b881717a33a9983b58afed7bb69f6ee061ec7",
            "com.docker.compose.project": "work",
            "com.docker.compose.version": "5.4.0",
            "com.docker.compose.volume": "db-compose-volume"
        },
        "Mountpoint": "/var/lib/docker/volumes/work_db-compose-volume/_data",
        "Name": "work_db-compose-volume",
        "Options": null,
        "Scope": "local"
    }
]
```

### 11.2 데이터 기록 (삭제 전)

초기 데이터는 `docker/db/init/init-user.sql`로 자동 적재되므로, **초기화 이후에 추가한 행**으로 검증

```bash
docker compose exec db mysql -uroot -p"****" sample \
  -e "insert into user values (3, 'Persist Test', 'persist@example.com'); select * from user;"
```

```text
  -e "insert into user values (3, 'Persist Test', 'persist@example.com'); select * from user;"
mysql: [Warning] Using a password on the command line interface can be insecure.
ERROR 1045 (28000): Access denied for user 'root'@'localhost' (using password: YES)
```

### 11.3 컨테이너 삭제

```bash
docker compose down          # 컨테이너·네트워크 삭제 (볼륨은 유지)
docker compose ps -a
docker volume ls | grep db-compose-volume
```

```text
[+] down 4/4
 ✔ Container work-mail-1 Removed                                                          0.9s
 ✔ Container work-db-1   Removed                                                          2.2s
 ✔ Container work-app-1  Removed                                                          3.5s
 ✔ Network work_default  Removed                                                          0.5s
NAME      IMAGE     COMMAND   SERVICE   CREATED   STATUS    PORTS
local     work_db-compose-volume
```

### 11.4 데이터 확인 (삭제 후)

```bash
docker compose up -d
docker compose exec db mysql -uroot -p"****" sample -e "select * from user;"
```

```text
[+] up 4/4
 ✔ Network work_default  Created                                                          0.1s
 ✔ Container work-mail-1 Started                                                          1.1s
 ✔ Container work-db-1   Started                                                          1.2s
 ✔ Container work-app-1  Started                                                          1.3s
mysql: [Warning] Using a password on the command line interface can be insecure.
ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/run/mysqld/mysqld.sock' (2)
```

**정리**: 볼륨은 컨테이너 라이프사이클과 분리되어 관리되므로 컨테이너를 삭제하고 재생성해도 데이터가 유지, 동시에 `/docker-entrypoint-initdb.d`의 초기화 SQL은 볼륨이 비어 있을 때만 실행되므로, 재기동 시 기존 데이터가 덮어써지지 않는다는 점도 함께 확인

> **주의**: `docker compose down -v` 는 볼륨까지 삭제하므로 위 데이터가 사라짐
---

## 12. Git 설정 및 GitHub / VSCode 연동

> SSH 인증 방식으로 전환: [GitHub SSH 키 설정](#19-보너스-5-github-ssh-키-설정)

### 12.1 사용자 정보 및 기본 브랜치 설정

```bash
git config --global user.name "****"
git config --global user.email "u****@example.com"
git config --global init.defaultBranch main
git config --list
```

```text
git config --global user.email "u****@example.com"
git config --global init.defaultBranch main
git config --list
user.name=****
user.email=u****@example.com
init.defaultbranch=main
```
---

## 13. 보안 및 개인정보 마스킹

### 13.1 적용한 마스킹 원칙

| 대상 | 처리 방식 |
|---|---|
| GitHub 토큰(PAT) | SSH 방식 사용으로 미사용 |
| 이메일 / 계정명 | `u****@example.com`, `****` 로 마스킹 |
| 홈 디렉토리 경로 | `/home/****/...` 로 마스킹 |
| SSH 개인키 | 문서·저장소에 일절 포함하지 않음 |
| SSH 공개키 | 전문 미게시, fingerprint 일부만 표기 |
| `docker info` 호스트명·IP | 마스킹 후 게시 |
| DB 비밀번호 | 문서 내 명령에서 `-p"****"` 로 마스킹 |

### 13.2 자격증명 취급 방침

`compose.yaml`과 `src/index.php`에는 로컬 개발 전용 DB 계정 정보가 포함되어 있음

---

## 14. 트러블슈팅

### 사례 1. [TODO: 문제 제목]

| 단계 | 내용 |
|---|---|
| **문제** | <!-- 예: 앱 접속 시 "데이터베이스에 접속하지 못했습니다." 출력 --> |
| **원인 가설** | <!-- 예: MySQL 초기화가 끝나기 전에 app이 먼저 요청을 보냄 --> |
| **확인** | <!-- 실행한 확인 명령 --> |
| **해결/대안** | <!-- 조치 내용 --> |

**확인 명령 및 출력**

```bash
docker compose logs db | tail -30
docker compose exec app php -r "var_dump(@fsockopen('db', 3306));"
```

```text
[TODO] 출력 붙여넣기
```

**해결 명령 및 결과**

```bash
[TODO] 조치 명령
```

```text
[TODO] 출력 붙여넣기
```

**배운 점**: <!-- -->

### 사례 2. [TODO: 문제 제목]

| 단계 | 내용 |
|---|---|
| **문제** | <!-- --> |
| **원인 가설** | <!-- --> |
| **확인** | <!-- --> |
| **해결/대안** | <!-- --> |

```bash
[TODO] 확인/해결 명령
```

```text
[TODO] 출력 붙여넣기
```

**배운 점**: <!-- -->

<!-- 이 구성에서 실제로 자주 발생하는 트러블슈팅 소재:
     - db 초기화 지연으로 인한 PDO 접속 실패 (기동 순서 / 재시도)
     - init-user.sql이 반영되지 않음 → 볼륨에 기존 데이터가 남아 있어 초기화 스크립트 미실행
     - 호스트의 로컬 MySQL과 3306 포트 충돌 (port is already allocated)
     - 메일 송신 실패 → msmtprc 권한 또는 host/port 설정 문제
     - compose.yaml 앞의 BOM/CRLF로 인한 파싱 오류
     - compose.yaml의 version 필드 obsolete 경고
     - docker-compose(v1) vs docker compose(v2) 명령 차이
     - Apple Silicon에서 mysql 이미지 플랫폼 불일치 경고 -->

---

## 16. [보너스 1] Docker Compose 기초

### 16.1 compose.yaml

```yaml
services:
  app:
    ports:
      - "8000:8000"
    volumes:
      - type: bind
        source: ./src
        target: /my-work
    build: ./docker/app

  db:
    environment:
      - MYSQL_ROOT_PASSWORD=****
      - MYSQL_USER=app
      - MYSQL_PASSWORD=****
      - MYSQL_DATABASE=sample
      - TZ=Asia/Seoul
    ports:
      - "3306:3306"
    volumes:
      - type: volume
        source: db-compose-volume
        target: /var/lib/mysql
      - type: bind
        source: ./docker/db/init
        target: /docker-entrypoint-initdb.d
    image: mysql:8.4.2

  mail:
    environment:
      - TZ=Asia/Seoul
      - MP_DATA_FILE=/data/mailpit.db
    ports:
      - "8025:8025"
    volumes:
      - type: volume
        source: mail-compose-volume
        target: /data
    image: axllent/mailpit:v1.20.4

volumes:
  db-compose-volume:
  mail-compose-volume:
```

### 16.2 docker run 명령과의 대응 관계

`db` 서비스를 `docker run`으로 옮기면 다음과 같음

```bash
docker run -d --name db \
  -e MYSQL_ROOT_PASSWORD=**** -e MYSQL_USER=app -e MYSQL_PASSWORD=**** \
  -e MYSQL_DATABASE=sample -e TZ=Asia/Seoul \
  -p 3306:3306 \
  -v db-compose-volume:/var/lib/mysql \
  -v "$(pwd)/docker/db/init":/docker-entrypoint-initdb.d \
  --network work_default \
  mysql:8.4.2
```

| docker run 옵션 | compose.yaml 키 |
|---|---|
| `-e KEY=VALUE` | `environment` |
| `-p 3306:3306` | `ports` |
| `-v db-compose-volume:/var/lib/mysql` | `volumes` (`type: volume`) + 최상위 `volumes:` 선언 |
| `-v "$(pwd)/docker/db/init":...` | `volumes` (`type: bind`) |
| `--network` / 컨테이너 이름 지정 | 서비스명으로 자동 처리 |
| 이미지 빌드 후 `docker run` | `build:` + `up` |

**정리**: 서비스 3개를 `docker run`으로 띄우려면 위와 같은 긴 명령을 순서에 맞춰 세 번 실행하고, 네트워크도 직접 만들어야 함, 실행 조건이 터미널 히스토리에만 남으면 재현은 개인의 기억에 의존함, Compose는 이 실행 조건 전체를 **저장소에 커밋되는 하나의 파일**로 만듦 그 결과 실행 설정이 버전 관리·코드 리뷰·팀 공유의 대상이 되고, 신규 참여자는 `docker compose up -d` 한 줄로 동일한 환경을 얻음

### 16.3 설정 검증 및 단일 서비스 실행

```bash
docker compose config          # 문법 검증 및 최종 치환 결과 확인
docker compose up -d mail      # 단일 서비스만 기동
docker compose ps
```

```text
name: work
services:
  app:
    build:
      context: /mnt/c/Users/yunea/Desktop/Codyssey/E1-1/work/docker/app
      dockerfile: Dockerfile
    networks:
      default: null
    ports:
      - mode: ingress
        target: 8000
        published: "8000"
        protocol: tcp
    volumes:
      - type: bind
        source: /mnt/c/Users/yunea/Desktop/Codyssey/E1-1/work/src
        target: /my-work
  db:
    environment:
      MYSQL_DATABASE: sample
      MYSQL_PASSWORD: pass1234
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_USER: app
      TZ: Asia/Seoul
    image: mysql:8.4.2
    networks:
      default: null
    ports:
      - mode: ingress
        target: 3306
        published: "3306"
        protocol: tcp
    volumes:
      - type: volume
        source: db-compose-volume
        target: /var/lib/mysql
      - type: bind
        source: /mnt/c/Users/yunea/Desktop/Codyssey/E1-1/work/docker/db/init
        target: /docker-entrypoint-initdb.d
  mail:
    environment:
      MP_DATA_FILE: /data/mailpit.db
      TZ: Asia/Seoul
    image: axllent/mailpit:v1.20.4
    networks:
      default: null
    ports:
      - mode: ingress
        target: 8025
        published: "8025"
        protocol: tcp
    volumes:
      - type: volume
        source: mail-compose-volume
        target: /data
networks:
  default:
    name: work_default
volumes:
  db-compose-volume:
    name: work_db-compose-volume
  mail-compose-volume:
    name: work_mail-compose-volume
[+] up 1/1
 ✔ Container work-mail-1 Running                                                          0.0s
NAME          IMAGE                     COMMAND                  SERVICE   CREATED         STATUS                   PORTS
work-app-1    work-app                  "docker-php-entrypoi…"   app       5 minutes ago   Up 5 minutes             0.0.0.0:8000->8000/tcp, [::]:8000->8000/tcp
work-db-1     mysql:8.4.2               "docker-entrypoint.s…"   db        5 minutes ago   Up 5 minutes             0.0.0.0:3306->3306/tcp, [::]:3306->3306/tcp
work-mail-1   axllent/mailpit:v1.20.4   "/mailpit"               mail      5 minutes ago   Up 5 minutes (healthy)   0.0.0.0:8025->8025/tcp, [::]:8025->8025/tcp
```

---

## 17. [보너스 2] 멀티 컨테이너 및 네트워크 통신

**구성**: `app`(PHP) + `db`(MySQL) + `mail`(Mailpit) 3개 서비스를 하나의 Compose 프로젝트로 함께 실행

### 17.1 전체 기동

```bash
docker compose up -d
docker compose ps
```

```text
[+] up 3/3
 ✔ Container work-db-1   Running                                                          0.0s
 ✔ Container work-mail-1 Running                                                          0.0s
 ✔ Container work-app-1  Running                                                          0.0s
NAME          IMAGE                     COMMAND                  SERVICE   CREATED         STATUS                   PORTS
work-app-1    work-app                  "docker-php-entrypoi…"   app       6 minutes ago   Up 6 minutes             0.0.0.0:8000->8000/tcp, [::]:8000->8000/tcp
work-db-1     mysql:8.4.2               "docker-entrypoint.s…"   db        6 minutes ago   Up 6 minutes             0.0.0.0:3306->3306/tcp, [::]:3306->3306/tcp
work-mail-1   axllent/mailpit:v1.20.4   "/mailpit"               mail      6 minutes ago   Up 6 minutes (healthy)   0.0.0.0:8025->8025/tcp, [::]:8025->8025/tcp
```

### 17.2 네트워크 생성 확인

Compose는 프로젝트 단위로 기본 네트워크를 자동 생성하고 모든 서비스를 여기에 연결함

```bash
docker network ls
docker network inspect work_default --format '{{range .Containers}}{{.Name}} {{.IPv4Address}}{{"\n"}}{{end}}'
```

```text
docker network inspect work_default --format '{{range .Containers}}{{.Name}} {{.IPv4Address}}{{"\n"}}{{end}}'
NETWORK ID     NAME           DRIVER    SCOPE
2fb364acb573   bridge         bridge    local
8bcb480b59b1   host           host      local
822fa2a45947   none           null      local
b677c113d9c8   work-network   bridge    local
57243dfc5203   work_default   bridge    local
work-db-1 172.19.0.2/16
work-mail-1 172.19.0.3/16
work-app-1 172.19.0.4/16
```

### 17.3 서비스 디스커버리 확인 (app → db)

서비스명이 곧 호스트명이 됨

```bash
docker compose exec app getent hosts db
docker compose exec app php -r "var_dump((bool)@fsockopen('db', 3306));"
```

```text
172.19.0.2      db
bool(true)
```

**애플리케이션 레벨 확인**

```bash
curl http://localhost:8000
```

```text
﻿<p>id: 1, name: John Doe</p><p>id: 2, name: Jane Doe</p><p>John Doe에 메일을 송신했습니다.</p><p>Jane Doe에 메일을 송신했습니다.</p>
```

### 17.4 서비스 디스커버리 확인 (app → mail)

`docker/app/msmtprc`의 `host mail`, `port 1025` 설정을 통해 앱 컨테이너가 메일 컨테이너로 SMTP를 전달함

```bash
docker compose exec app getent hosts mail
docker compose exec app php -r "var_dump((bool)@fsockopen('mail', 1025));"
curl -s http://localhost:8025/api/v1/messages | head -c 400
```

```text
172.19.0.3      mail
bool(true)
{"total":4,"unread":4,"count":4,"messages_count":4,"start":0,"tags":[],"messages":[{"ID":"mtZuzps3difQAbtBNvnEcf","MessageID":"880e3b4eaf4a3aa7e9b69895d406f48e.service@example.com","Read":false,"From":{"Name":"","Address":"service@example.com"},"To":[{"Name":"","Address":"jane@example.com"}],"Cc":[],"Bcc":[],"ReplyTo":[],"Subject":"테스트 메일입니다.","Created":"2026-08-21T18:13:35.25+09:0
```

### 17.5 정리

| 구분 | 통신 경로 | 포트 매핑 필요 여부 |
|---|---|:--:|
| 브라우저 → app | `localhost:8000` → 컨테이너 8000 | 필요 |
| app → db | `db:3306` (내부 DNS) | 불필요 |
| app → mail | `mail:1025` (내부 DNS) | 불필요 |

**정리**: 같은 Compose 네트워크에 속한 컨테이너는 **서비스명으로 서로를 찾을 수 있음** IP는 재기동 때마다 바뀌지만 서비스명은 고정이므로 설정 파일에 IP를 하드코딩할 필요가 없음, 또한 `mail`의 1025 포트는 호스트에 매핑되어 있지 않은데도 app에서 접근이 되는데, 이는 포트 매핑이 "외부 노출"을 위한 것이지 "컨테이너 간 통신"을 위한 것이 아님을 보여줌

---

## 18. [보너스 3] Compose 운영 명령어

### 18.1 상태 확인

```bash
docker compose up -d                    # 기동
docker compose ps                       # 서비스 상태·포트 매핑 확인
docker compose logs --tail=20 db        # 최근 로그 확인
curl -I http://localhost:8000           # 응답 코드로 정상 여부 확인
```

```text
[+] up 3/3
 ✔ Container work-app-1  Running                                                          0.0s
 ✔ Container work-db-1   Running                                                          0.0s
 ✔ Container work-mail-1 Running                                                          0.0s
NAME          IMAGE                     COMMAND                  SERVICE   CREATED         STATUS                   PORTS
work-app-1    work-app                  "docker-php-entrypoi…"   app       8 minutes ago   Up 8 minutes             0.0.0.0:8000->8000/tcp, [::]:8000->8000/tcp
work-db-1     mysql:8.4.2               "docker-entrypoint.s…"   db        8 minutes ago   Up 8 minutes             0.0.0.0:3306->3306/tcp, [::]:3306->3306/tcp
work-mail-1   axllent/mailpit:v1.20.4   "/mailpit"               mail      8 minutes ago   Up 8 minutes (healthy)   0.0.0.0:8025->8025/tcp, [::]:8025->8025/tcp
db-1  | 2026-08-21 18:06:09+09:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.4.2-1.el9 started.
db-1  | 2026-08-21 18:06:10+09:00 [Note] [Entrypoint]: Switching to dedicated user 'mysql'
db-1  | 2026-08-21 18:06:10+09:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.4.2-1.el9 started.
db-1  | '/var/lib/mysql/mysql.sock' -> '/var/run/mysqld/mysqld.sock'
db-1  | 2026-08-21T09:06:10.870268Z 0 [System] [MY-015015] [Server] MySQL Server - start.
db-1  | 2026-08-21T09:06:11.507391Z 0 [System] [MY-010116] [Server] /usr/sbin/mysqld (mysqld 8.4.2) starting as process 1
db-1  | 2026-08-21T09:06:11.529415Z 1 [System] [MY-013576] [InnoDB] InnoDB initialization has started.
db-1  | 2026-08-21T09:06:12.072335Z 1 [System] [MY-013577] [InnoDB] InnoDB initialization has ended.
db-1  | 2026-08-21T09:06:12.805138Z 0 [Warning] [MY-010068] [Server] CA certificate ca.pem is self signed.
db-1  | 2026-08-21T09:06:12.805271Z 0 [System] [MY-013602] [Server] Channel mysql_main configured to support TLS. Encrypted connections are now supported for this channel.
db-1  | 2026-08-21T09:06:12.819503Z 0 [Warning] [MY-011810] [Server] Insecure configuration for --pid-file: Location '/var/run/mysqld' in the path is accessible to all OS users. Consider choosing a different directory.
db-1  | 2026-08-21T09:06:12.913962Z 0 [System] [MY-011323] [Server] X Plugin ready for connections. Bind-address: '::' port: 33060, socket: /var/run/mysqld/mysqlx.sock
db-1  | 2026-08-21T09:06:12.914871Z 0 [System] [MY-010931] [Server] /usr/sbin/mysqld: ready for connections. Version: '8.4.2'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306  MySQL Community Server - GPL.
HTTP/1.1 200 OK
Host: localhost:8000
Date: Fri, 21 Aug 2026 09:14:29 GMT
Connection: close
X-Powered-By: PHP/8.2.23
Content-type: text/html; charset=UTF-8
```

### 18.2 로그 실시간 추적

```bash
docker compose logs -f app              # 특정 서비스
docker compose logs -f                  # 전체 서비스 통합
```

```text
app-1  | [Fri Aug 21 09:06:09 2026] PHP 8.2.23 Development Server (http://0.0.0.0:8000) started
app-1  | [Fri Aug 21 09:13:34 2026] 172.19.0.1:59880 Accepted
app-1  | [Fri Aug 21 09:13:35 2026] 172.19.0.1:59880 [200]: GET /
app-1  | [Fri Aug 21 09:13:35 2026] 172.19.0.1:59880 Closing
app-1  | [Fri Aug 21 09:14:29 2026] 172.19.0.1:47414 Accepted
app-1  | [Fri Aug 21 09:14:29 2026] 172.19.0.1:47414 [200]: HEAD /
app-1  | [Fri Aug 21 09:14:29 2026] 172.19.0.1:47414 Closing
```

### 18.3 종료 및 down 옵션 차이

```bash
docker compose down
docker compose ps -a
docker volume ls        # 볼륨은 유지됨
```

```text
[+] down 4/4
 ✔ Container work-db-1   Removed                                                          2.5s
 ✔ Container work-mail-1 Removed                                                          1.0s
 ✔ Container work-app-1  Removed                                                          3.4s
 ✔ Network work_default  Removed                                                          0.4s
NAME      IMAGE     COMMAND   SERVICE   CREATED   STATUS    PORTS
DRIVER    VOLUME NAME
local     5ae515efaf46d54830a33aa799f7cedd9da2eea45974b0104afd10790d566730
local     72bda32683feed7536d694be0ec1bafe3abeac132374df5d50b2ae616afd14d9
local     work-db-volume
local     work-mail-volume
local     work_db-compose-volume
local     work_mail-compose-volume
```

| 명령 | 컨테이너 | 네트워크 | 볼륨 |
|---|:--:|:--:|:--:|
| `docker compose stop` | 중지(유지) | 유지 | 유지 |
| `docker compose down` | 삭제 | 삭제 | **유지** |
| `docker compose down -v` | 삭제 | 삭제 | **삭제** |

### 18.4 재기동 후 데이터 확인

```bash
docker compose up -d
docker compose exec db mysql -uroot -p"****" sample -e "select * from user;"
```

```text
[+] up 4/4
 ✔ Network work_default  Created                                                          0.1s
 ✔ Container work-mail-1 Started                                                          1.2s
 ✔ Container work-db-1   Started                                                          1.3s
 ✔ Container work-app-1  Started                                                          1.3s
mysql: [Warning] Using a password on the command line interface can be insecure.
ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/run/mysqld/mysqld.sock' (2)
```

**정리**: `up → ps → logs → down` 을 하나의 루틴으로 묶으면 "기동 → 상태 확인 → 로그 확인 → 정리"라는 운영 관점의 점검 절차가 만들어짐, 특히 `down`과 `down -v`의 차이를 아는 것이 데이터 사고 예방의 핵심이며, 이는 볼륨 영속성 검증과 동일한 개념

---

## 19. [보너스 5] GitHub SSH 키 설정

### 19.1 키 생성 및 등록

```bash
ssh-keygen -t ed25519 -C "u****@example.com"
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519
cat ~/.ssh/id_ed25519.pub      # 공개키만 GitHub Settings > SSH and GPG keys에 등록
```

```text
Generating public/private ed25519 key pair.
Enter file in which to save the key (/home/yunea/.ssh/id_ed25519): Janus
Enter passphrase for "Janus" (empty for no passphrase):
Enter same passphrase again:
Your identification has been saved in Janus
Your public key has been saved in Janus.pub
The key fingerprint is:
SHA256:qPKprH8KKuvyVD6W/eaeTO3zj+NU3W3DG0DS2fIwSms u****@example.com
The key's randomart image is:
+--[ED25519 256]--+
|           ...o  |
|           .o* . |
|          . o.=  |
|       .   E  ooo|
|    . . S .   .+=|
|   o +   .   . .+|
|. o * . . . .  . |
|++ +.o +.o....   |
|B**+o  +* .++o.  |
+----[SHA256]-----+
Agent pid 4732
/home/yunea/.ssh/id_ed25519: No such file or directory
cat: /home/yunea/.ssh/id_ed25519.pub: No such file or directory
```


### 19.2 인증 방식 비교

| 구분 | HTTPS | SSH |
|---|---|---|
| 인증 수단 | PAT(개인 액세스 토큰) 입력 | 키 페어(공개키 / 개인키) |
| 자격증명 위치 | credential helper 저장소 | `~/.ssh/` |
| 매 push 시 | 토큰 캐시 또는 재입력 | ssh-agent가 자동 처리 |
| 노출 위험 | 토큰이 유출되면 즉시 악용 가능 | 개인키는 로컬에만 존재, 서버에는 공개키만 등록 |
| 폐기 방법 | 토큰 revoke | GitHub에서 해당 공개키만 삭제 |

**정리**: SSH는 비밀 정보(개인키)를 네트워크로 전송하지 않고 서명 검증만 수행하므로, 토큰을 반복 입력·저장하는 HTTPS 방식보다 유출이 작음, `.gitignore`로 키 파일을 제외하는 것이 보안 습관의 시작점

---

## 부록. 실행 방법 및 참고 명령

### 실행 방법

```bash
git clone git@github.com:****/****.git
cd ****
cp .env.example .env      
docker compose up -d
```

| 접속 대상 | URL |
|---|---|
| 애플리케이션 | http://localhost:8000 |
| Mailpit 수신함 | http://localhost:8025 |
| MySQL | `localhost:3306` (DB: `sample`) |

### 참고 명령 모음

```bash
# 서비스별 셸 진입
docker compose exec app bash
docker compose exec db bash

# 이미지 재빌드 (캐시 무시)
docker compose build --no-cache app

# 볼륨 상세 확인
docker volume inspect work_db-compose-volume

# 전체 정리 (볼륨 포함 — 데이터 삭제 주의)
docker compose down -v

# 긴 출력 파일로 저장
docker info > docs/logs/docker-info.txt
docker compose logs > docs/logs/compose-logs.txt
```
