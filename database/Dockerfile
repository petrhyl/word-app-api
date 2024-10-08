# Use an official MySQL image as a parent image
FROM mysql:latest

COPY ["sql-scripts/01-database-init.sql", "sql-scripts/"]
COPY ./sql-scripts/seed/ /sql-scripts/seed/
COPY ./database-seed.sh /database-seed.sh
RUN chmod +x /database-seed.sh

# Start MySQL service (default command of the MySQL image)
ENTRYPOINT [ "/database-seed.sh" ]
